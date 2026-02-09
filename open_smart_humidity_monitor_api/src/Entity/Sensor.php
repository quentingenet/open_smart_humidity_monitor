<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SensorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SensorRepository::class)]
#[ORM\Table(name: 'sensor')]
class Sensor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $name = '';

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $location = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private string $humidityThreshold = '0';

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => true])]
    private bool $isActive = true;

    #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
    private string $apiKey = '';

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, HumidityMeasurement>
     */
    #[ORM\OneToMany(targetEntity: HumidityMeasurement::class, mappedBy: 'sensor', orphanRemoval: true)]
    private Collection $humidityMeasurements;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->humidityMeasurements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): static
    {
        $this->location = $location;
        return $this;
    }

    public function getHumidityThreshold(): string
    {
        return $this->humidityThreshold;
    }

    public function setHumidityThreshold(string $humidityThreshold): static
    {
        $this->humidityThreshold = $humidityThreshold;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function setApiKey(string $apiKey): static
    {
        $this->apiKey = $apiKey;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return Collection<int, HumidityMeasurement>
     */
    public function getHumidityMeasurements(): Collection
    {
        return $this->humidityMeasurements;
    }

    public function addHumidityMeasurement(HumidityMeasurement $humidityMeasurement): static
    {
        if (!$this->humidityMeasurements->contains($humidityMeasurement)) {
            $this->humidityMeasurements->add($humidityMeasurement);
            $humidityMeasurement->setSensor($this);
        }
        return $this;
    }

    public function removeHumidityMeasurement(HumidityMeasurement $humidityMeasurement): static
    {
        if ($this->humidityMeasurements->removeElement($humidityMeasurement)) {
            if ($humidityMeasurement->getSensor() === $this) {
                $humidityMeasurement->setSensor(null);
            }
        }
        return $this;
    }
}
