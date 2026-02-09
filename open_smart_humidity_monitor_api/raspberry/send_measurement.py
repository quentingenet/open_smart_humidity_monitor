#!/usr/bin/env python3
"""
Read DHT22 humidity on Raspberry Pi and POST to Open Smart Humidity Monitor API.
Configure via env: API_BASE_URL, SENSOR_API_KEY.
Optional: DHT_PIN (BCM, default 4).
"""

import os
import sys
import time

try:
    import adafruit_dht
    import board
    import requests
except ImportError as e:
    print(f"Missing dependency: {e}", file=sys.stderr)
    print("Install: pip install -r requirements.txt", file=sys.stderr)
    sys.exit(2)

API_BASE_URL = os.environ.get("API_BASE_URL", "http://localhost:8000").rstrip("/")
SENSOR_API_KEY = os.environ.get("SENSOR_API_KEY", "")
DHT_PIN = int(os.environ.get("DHT_PIN", "4"))
MEASUREMENTS_URL = f"{API_BASE_URL}/api/measurements"


def read_dht22(pin_bcm: int) -> float | None:
    """Read humidity from DHT22 on given BCM pin. Returns None on read error."""
    dht = adafruit_dht.DHT22(getattr(board, f"D{pin_bcm}"))
    try:
        for _ in range(3):
            humidity = dht.humidity
            if humidity is not None:
                return round(float(humidity), 2)
            time.sleep(0.5)
    except RuntimeError:
        pass
    finally:
        try:
            dht.exit()
        except Exception:
            pass
    return None


def send_measurement(api_key: str, humidity: float) -> bool:
    """POST one measurement to the API. Returns True on success."""
    resp = requests.post(
        MEASUREMENTS_URL,
        json={"humidity": humidity},
        headers={"X-Api-Key": api_key, "Content-Type": "application/json"},
        timeout=10,
    )
    if resp.status_code == 201:
        return True
    print(f"API error {resp.status_code}: {resp.text}", file=sys.stderr)
    return False


def main() -> int:
    if not SENSOR_API_KEY:
        print("Set SENSOR_API_KEY (and optionally API_BASE_URL, DHT_PIN).", file=sys.stderr)
        return 1

    humidity = read_dht22(DHT_PIN)
    if humidity is None:
        print("DHT22 read failed.", file=sys.stderr)
        return 1

    if not send_measurement(SENSOR_API_KEY, humidity):
        return 1

    print(f"Sent humidity={humidity}%")
    return 0


if __name__ == "__main__":
    sys.exit(main())
