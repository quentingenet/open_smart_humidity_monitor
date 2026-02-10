<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class SubmitMeasurementRequest
{
    #[Assert\NotNull]
    #[Assert\Type('numeric')]
    #[Assert\Range(min: 0, max: 100)]
    public mixed $humidity = null;

    #[Assert\Type('string')]
    public ?string $measuredAt = null;
}

