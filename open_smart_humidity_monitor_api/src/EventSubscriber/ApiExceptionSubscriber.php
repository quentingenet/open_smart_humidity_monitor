<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Exception\ApiProblemException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class ApiExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onException',
        ];
    }

    public function onException(ExceptionEvent $event): void
    {
        $request = $event->getRequest();
        if (!str_starts_with($request->getPathInfo(), '/api/')) {
            return;
        }

        $throwable = $event->getThrowable();

        if ($throwable instanceof ApiProblemException) {
            $status = $throwable->status;
            $problem = [
                'type' => $throwable->type,
                'title' => $throwable->title,
                'status' => $status,
                'detail' => $throwable->detail,
            ] + $throwable->extra;
        } elseif ($throwable instanceof HttpExceptionInterface) {
            $status = $throwable->getStatusCode();
            $problem = [
                'type' => 'about:blank',
                'title' => JsonResponse::$statusTexts[$status] ?? 'Error',
                'status' => $status,
                'detail' => $throwable->getMessage() !== '' ? $throwable->getMessage() : 'HTTP error',
            ];
        } else {
            $status = 500;
            $problem = [
                'type' => 'about:blank',
                'title' => 'Internal Server Error',
                'status' => $status,
                'detail' => 'Unexpected error',
            ];
        }

        $response = new JsonResponse(
            $problem,
            $status,
            ['Content-Type' => 'application/problem+json']
        );

        $event->setResponse($response);
    }
}

