<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\SensorSummary;
use App\Entity\Sensor;
use App\Repository\HumidityMeasurementRepository;

class SensorMonitoringService
{
    public function __construct(
        private readonly HumidityMeasurementRepository $humidityMeasurementRepository,
    ) {
    }

    public function getSensorSummary(Sensor $sensor): SensorSummary
    {
        $latest = $this->humidityMeasurementRepository->findLatestForSensor($sensor);
        $average7Days = $this->humidityMeasurementRepository->getAverageHumidityForSensor($sensor, 7);

        $isAlert = $latest !== null
            && (float) $latest->getHumidity() > (float) $sensor->getHumidityThreshold();

        return new SensorSummary(
            $latest?->getHumidity(),
            $average7Days,
            $isAlert,
            (float) $sensor->getHumidityThreshold(),
        );
    }
}
