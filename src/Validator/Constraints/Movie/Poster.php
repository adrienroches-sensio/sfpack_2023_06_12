<?php

declare(strict_types=1);

namespace App\Validator\Constraints\Movie;

use Attribute;
use Symfony\Component\Validator\Constraints\AtLeastOneOf;
use Symfony\Component\Validator\Constraints\Compound;
use Symfony\Component\Validator\Constraints\Url;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD)]
final class Poster extends Compound
{
    protected function getConstraints(array $options): array
    {
        return [
            new AtLeastOneOf([
                new PosterExists(),
                new Url()
            ])
        ];
    }
}
