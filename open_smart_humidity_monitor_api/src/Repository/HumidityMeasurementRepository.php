<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\HumidityMeasurement;
use App\Entity\Sensor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HumidityMeasurement>
 */
class HumidityMeasurementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HumidityMeasurement::class);
    }

    public function findLatestForSensor(Sensor $sensor): ?HumidityMeasurement
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.sensor = :sensor')
            ->setParameter('sensor', $sensor)
            ->orderBy('h.measuredAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getAverageHumidityForSensor(Sensor $sensor, int $days): ?float
    {
        $since = (new \DateTimeImmutable())->modify("-{$days} days");

        $result = $this->createQueryBuilder('h')
            ->select('AVG(h.humidity) as avgHumidity')
            ->andWhere('h.sensor = :sensor')
            ->andWhere('h.measuredAt >= :since')
            ->setParameter('sensor', $sensor)
            ->setParameter('since', $since)
            ->getQuery()
            ->getSingleScalarResult();

        return $result !== null ? (float) $result : null;
    }

    /**
     * @return list<HumidityMeasurement>
     */
    public function findHistoryForSensor(
        Sensor $sensor,
        ?\DateTimeImmutable $from = null,
        ?\DateTimeImmutable $to = null,
        int $limit = 500
    ): array {
        $qb = $this->createQueryBuilder('h')
            ->andWhere('h.sensor = :sensor')
            ->setParameter('sensor', $sensor)
            ->orderBy('h.measuredAt', 'ASC')
            ->setMaxResults($limit);

        if ($from !== null) {
            $qb->andWhere('h.measuredAt >= :from')->setParameter('from', $from);
        }
        if ($to !== null) {
            $qb->andWhere('h.measuredAt <= :to')->setParameter('to', $to);
        }

        return $qb->getQuery()->getResult();
    }
}
