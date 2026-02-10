<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Sensor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sensor>
 */
class SensorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sensor::class);
    }

    public function findOneByApiKey(string $apiKey): ?Sensor
    {
        $sensors = $this->findBy(['isActive' => true]);

        foreach ($sensors as $sensor) {
            if (password_verify($apiKey, $sensor->getApiKey())) {
                return $sensor;
            }
        }

        return null;
    }
}
