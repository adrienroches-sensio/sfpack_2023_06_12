<?php

declare(strict_types=1);

namespace App\Validator\Constraints\Movie;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use function file_exists;

final class PosterExistsValidator extends ConstraintValidator
{
    public function __construct(
        private readonly string $assetsImagesMoviesPath,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof PosterExists) {
            throw new UnexpectedTypeException($constraint, PosterExists::class);
        }

        if (null === $value) {
            return;
        }

        $filename = "{$this->assetsImagesMoviesPath}/{$value}";
        if (file_exists($filename) === false) {
            $violationBuilder = $this->context->buildViolation($constraint->message)
                ->setParameter('{{ filename }}', $this->formatValue($value))
                ->setInvalidValue($value)
            ;

            $violationBuilder->addViolation();

            return;
        }
    }
}
