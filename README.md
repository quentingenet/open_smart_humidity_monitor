![Symfony](https://img.shields.io/badge/Symfony-000000?style=for-the-badge&logo=symfony)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MariaDB](https://img.shields.io/badge/MariaDB-003545?style=for-the-badge&logo=mariadb)
![Raspberry Pi](https://img.shields.io/badge/Raspberry%20Pi-C51A4A?style=for-the-badge&logo=raspberry-pi)
![Python](https://img.shields.io/badge/Python-3670A0?style=for-the-badge&logo=python&logoColor=ffdd54)
![Vue.js](https://img.shields.io/badge/Vue.js-35495E?style=for-the-badge&logo=vue.js&logoColor=4FC08D)
![Vuetify](https://img.shields.io/badge/Vuetify-1867C0?style=for-the-badge&logo=vuetify)

## Open Smart Humidity Monitor

Open-source IoT experimental project to monitor humidity levels using a Raspberry Pi and a Symfony REST API.

## What does this project do?

- Measures humidity using a DHT22 sensor
- Sends data from a Raspberry Pi to a backend API
- Stores measurements in a database (MariaDB)
- Computes:
  - latest humidity value
  - average humidity over the last 7 days
  - alert status based on a configurable threshold
- Exposes data through a clean REST API

## Tech stack

### Backend
- PHP 8.2+
- Symfony 7
- Doctrine ORM
- MariaDB

### IoT
- Raspberry Pi
- Python
- DHT22 / AM2302 humidity sensor

### Frontend (planned)
- Vue.js 3
- Vuetify
- Chart.js

## How to run the backend

### 1. Clone the repository

```bash
git clone https://github.com/quentingenet/open_smart_humidity_monitor.git
cd open_smart_humidity_monitor/open_smart_humidity_monitor_api
```

### 2. Environment configuration

```bash
cp .env.example .env.local
```

Edit `.env.local` and configure the database:

```env
DATABASE_URL="mysql://USER:PASSWORD@127.0.0.1:3306/open_smart_humidity_monitor?serverVersion=mariadb-10.11.0"
```

### 3. Install dependencies

```bash
composer install
```

### 4. Database setup

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 5. Run the API

```bash
symfony serve
```

The API will be available at:
https://127.0.0.1:8000

## Main API endpoints

- **GET** `/api/sensors/{id}/summary`  
  Returns latest humidity, 7-day average and alert status.

- **POST** `/api/measurements`  
  Receives humidity data from the Raspberry Pi.

IoT devices authenticate using an API key sent in the `X-API-KEY` HTTP header.

## License

This project is released under the **GNU General Public License v3.0 (GPL-3.0)**.

## Author

Quentin Genet
