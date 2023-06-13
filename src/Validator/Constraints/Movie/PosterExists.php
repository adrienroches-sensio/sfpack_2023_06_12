<?php

declare(strict_types=1);

namespace App\Validator\Constraints\Movie;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD)]
final class PosterExists extends Constraint
{
    public function __construct(
        public string $message = '{{ filename }} was not found on the system.',
        mixed $options = null,
        array $groups = null,
        mixed $payload = null
    ) {
        parent::__construct($options, $groups, $payload);
    }
}
