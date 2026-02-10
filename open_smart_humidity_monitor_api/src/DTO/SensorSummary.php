<?php

declare(strict_types=1);

namespace App\DTO;

final readonly class SensorSummary implements \JsonSerializable
{
    public function __construct(
        public ?float $latestHumidity,
        public ?float $average7Days,
        public bool $isAlert,
        public float $humidityThreshold,
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'latestHumidity' => $this->latestHumidity,
            'average7Days' => $this->average7Days,
            'isAlert' => $this->isAlert,
            'humidityThreshold' => $this->humidityThreshold,
        ];
    }
}
