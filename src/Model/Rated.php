<?php

declare(strict_types=1);

namespace App\Model;

enum Rated: string
{
    case GeneralAudiences = 'G';
    case ParentalGuidanceSuggested = 'PG';
    case ParentsStronglyCautioned = 'PG-13';
    case Restricted = 'R';
    case AdultsOnly = 'NC-17';

    public function minAgeRequired(): int
    {
        return match($this) {
            self::GeneralAudiences => 0,
            self::ParentalGuidanceSuggested, self::ParentsStronglyCautioned => 13,
            self::Restricted, self::AdultsOnly => 17,
        };
    }
}
