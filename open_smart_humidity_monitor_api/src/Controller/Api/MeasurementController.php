<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Exception\ApiProblemException;
use App\DTO\SubmitMeasurementRequest;
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
        private readonly \Symfony\Component\Validator\Validator\ValidatorInterface $validator,
    ) {
    }

    #[Route('', name: 'submit', methods: ['POST'])]
    public function submit(Request $request): JsonResponse
    {
        $apiKey = $request->headers->get(self::HEADER_API_KEY);
        if ($apiKey === null || $apiKey === '') {
            throw new ApiProblemException(
                'https://example.com/problems/missing-api-key',
                'Missing API key',
                'X-Api-Key header is required',
                Response::HTTP_UNAUTHORIZED,
            );
        }

        $sensor = $this->sensorRepository->findOneByApiKey($apiKey);
        if ($sensor === null) {
            throw new ApiProblemException(
                'https://example.com/problems/invalid-api-key',
                'Invalid API key',
                'X-Api-Key header does not match any sensor',
                Response::HTTP_FORBIDDEN,
            );
        }

        if (!$sensor->isActive()) {
            throw new ApiProblemException(
                'https://example.com/problems/sensor-disabled',
                'Sensor disabled',
                'Sensor is disabled',
                Response::HTTP_FORBIDDEN,
            );
        }

        try {
            $payload = json_decode((string) $request->getContent(), true, 512, \JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw new ApiProblemException(
                'https://example.com/problems/invalid-json',
                'Invalid JSON',
                'Request body is not valid JSON',
                Response::HTTP_BAD_REQUEST,
            );
        }

        if (!\is_array($payload)) {
            throw new ApiProblemException(
                'https://example.com/problems/invalid-payload',
                'Invalid payload',
                'Body must be a JSON object',
                Response::HTTP_BAD_REQUEST,
            );
        }

        $dto = new SubmitMeasurementRequest();
        $dto->humidity = $payload['humidity'] ?? null;
        $dto->measuredAt = isset($payload['measuredAt']) ? (string) $payload['measuredAt'] : null;

        $violations = $this->validator->validate($dto);
        if (\count($violations) > 0) {
            $errors = [];

            foreach ($violations as $violation) {
                $errors[] = [
                    'propertyPath' => $violation->getPropertyPath(),
                    'message' => $violation->getMessage(),
                ];
            }

            throw new ApiProblemException(
                'https://example.com/problems/validation-error',
                'Validation error',
                'Request payload is not valid',
                Response::HTTP_BAD_REQUEST,
                ['violations' => $errors],
            );
        }

        $humidityFloat = (float) $dto->humidity;

        $measuredAt = null;
        if (isset($payload['measuredAt']) && \is_string($payload['measuredAt'])) {
            try {
                $measuredAt = new \DateTimeImmutable($payload['measuredAt']);
            } catch (\Exception) {
                throw new ApiProblemException(
                    'https://example.com/problems/invalid-measured-at',
                    'Invalid measuredAt',
                    'measuredAt must be a valid ISO 8601 date-time',
                    Response::HTTP_BAD_REQUEST,
                );
            }
        }

        $humidityStr = (string) $humidityFloat;
        $measurement = $this->measurementSubmissionService->submit($sensor, $humidityStr, $measuredAt);

        return new JsonResponse([
            'id' => $measurement->getId(),
            'humidity' => (float) $measurement->getHumidity(),
            'measuredAt' => $measurement->getMeasuredAt()?->format(\DateTimeInterface::ATOM),
        ], Response::HTTP_CREATED);
    }
}
