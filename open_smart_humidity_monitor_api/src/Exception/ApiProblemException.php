<?php

declare(strict_types=1);

namespace App\Exception;

final class ApiProblemException extends \RuntimeException
{
    public function __construct(
        public readonly string $type,
        public readonly string $title,
        public readonly string $detail,
        public readonly int $status,
        public readonly array $extra = [],
    ) {
        parent::__construct($detail, $status);
    }
}

