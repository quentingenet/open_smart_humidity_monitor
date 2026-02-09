<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\HumidityMeasurementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HumidityMeasurementRepository::class)]
#[ORM\Table(name: 'humidity_measurement')]
class HumidityMeasurement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private string $humidity = '0';

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $measuredAt = null;

    #[ORM\ManyToOne(targetEntity: Sensor::class, inversedBy: 'humidityMeasurements')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Sensor $sensor = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHumidity(): string
    {
        return $this->humidity;
    }

    public function setHumidity(string $humidity): static
    {
        $this->humidity = $humidity;
        return $this;
    }

    public function getMeasuredAt(): ?\DateTimeImmutable
    {
        return $this->measuredAt;
    }

    public function setMeasuredAt(\DateTimeImmutable $measuredAt): static
    {
        $this->measuredAt = $measuredAt;
        return $this;
    }

    public function getSensor(): ?Sensor
    {
        return $this->sensor;
    }

    public function setSensor(?Sensor $sensor): static
    {
        $this->sensor = $sensor;
        return $this;
    }
}
