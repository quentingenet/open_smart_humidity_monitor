<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Exception\ApiProblemException;
use App\Repository\SensorRepository;
use App\Service\SensorMonitoringService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/sensors', name: 'api_sensor_')]
final class SensorController extends AbstractController
{
    public function __construct(
        private readonly SensorRepository $sensorRepository,
        private readonly SensorMonitoringService $sensorMonitoringService,
    ) {
    }

    #[Route('/{id}/summary', name: 'summary', methods: ['GET'])]
    public function summary(int $id): JsonResponse
    {
        $sensor = $this->sensorRepository->find($id);
        if ($sensor === null) {
            throw new ApiProblemException(
                'https://example.com/problems/sensor-not-found',
                'Sensor not found',
                'Sensor not found',
                Response::HTTP_NOT_FOUND,
            );
        }

        $summary = $this->sensorMonitoringService->getSensorSummary($sensor);

        return new JsonResponse($summary);
    }
}
