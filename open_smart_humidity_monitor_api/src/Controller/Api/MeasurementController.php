<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Repository\SensorRepository;
use App\Service\MeasurementSubmissionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/measurements', name: 'api_measurement_')]
final class MeasurementController extends AbstractController
{
    private const HEADER_API_KEY = 'X-Api-Key';

    public function __construct(
        private readonly SensorRepository $sensorRepository,
        private readonly MeasurementSubmissionService $measurementSubmissionService,
    ) {
    }

    #[Route('', name: 'submit', methods: ['POST'])]
    public function submit(Request $request): JsonResponse
    {
        $apiKey = $request->headers->get(self::HEADER_API_KEY);
        if ($apiKey === null || $apiKey === '') {
            return new JsonResponse(
                ['error' => 'Missing X-Api-Key header'],
                Response::HTTP_UNAUTHORIZED
            );
        }

        $sensor = $this->sensorRepository->findOneByApiKey($apiKey);
        if ($sensor === null) {
            return new JsonResponse(
                ['error' => 'Invalid API key'],
                Response::HTTP_FORBIDDEN
            );
        }

        if (!$sensor->isActive()) {
            return new JsonResponse(
                ['error' => 'Sensor is disabled'],
                Response::HTTP_FORBIDDEN
            );
        }

        $payload = json_decode((string) $request->getContent(), true);
        if (!\is_array($payload) || !isset($payload['humidity'])) {
            return new JsonResponse(
                ['error' => 'Body must contain "humidity" (number)'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $humidity = $payload['humidity'];
        if (!is_numeric($humidity)) {
            return new JsonResponse(
                ['error' => 'humidity must be a number'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $measuredAt = null;
        if (isset($payload['measuredAt']) && \is_string($payload['measuredAt'])) {
            try {
                $measuredAt = new \DateTimeImmutable($payload['measuredAt']);
            } catch (\Exception) {
                return new JsonResponse(
                    ['error' => 'measuredAt must be a valid ISO 8601 date-time'],
                    Response::HTTP_BAD_REQUEST
                );
            }
        }

        $humidityStr = (string) (is_float($humidity) || is_int($humidity) ? $humidity : (float) $humidity);
        $measurement = $this->measurementSubmissionService->submit($sensor, $humidityStr, $measuredAt);

        return new JsonResponse([
            'id' => $measurement->getId(),
            'humidity' => $measurement->getHumidity(),
            'measuredAt' => $measurement->getMeasuredAt()?->format(\DateTimeInterface::ATOM),
        ], Response::HTTP_CREATED);
    }
}
