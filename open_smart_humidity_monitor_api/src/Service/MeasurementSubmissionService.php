<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\HumidityMeasurement;
use App\Entity\Sensor;
use Doctrine\ORM\EntityManagerInterface;

class MeasurementSubmissionService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function submit(Sensor $sensor, string $humidity, ?\DateTimeImmutable $measuredAt = null): HumidityMeasurement
    {
        $measurement = new HumidityMeasurement();
        $measurement->setSensor($sensor);
        $measurement->setHumidity($humidity);
        $measurement->setMeasuredAt($measuredAt ?? new \DateTimeImmutable());

        $this->entityManager->persist($measurement);
        $this->entityManager->flush();

        return $measurement;
    }
}
