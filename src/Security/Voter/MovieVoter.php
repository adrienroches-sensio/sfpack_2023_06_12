<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Model\Movie;
use Psr\Clock\ClockInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class MovieVoter extends Voter
{
    public const MEETS_AGE_REQUIREMENTS = 'MOVIE_MEETS_AGE_REQUIREMENTS';

    public function __construct(
        private readonly Security $security,
        private readonly ClockInterface $clock,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return self::MEETS_AGE_REQUIREMENTS === $attribute && $subject instanceof Movie;
    }

    /**
     * @param Movie $subject
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        if ($subject->rated->minAgeRequired() === 0) {
            return true;
        }

        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if ($this->security->isGranted('ROLE_ADMIN') === true) {
            return true;
        }

        return $user->isOlderThanOrEqual($subject->rated->minAgeRequired(), $this->clock->now());
    }
}
