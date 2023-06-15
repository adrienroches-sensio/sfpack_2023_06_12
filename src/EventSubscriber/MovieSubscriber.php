<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use function array_filter;

final class MovieSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly UserRepository $userRepository,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MovieAddedEvent::class => [
                ['notifyAllOtherAdmins']
            ],
        ];
    }

    public function notifyAllOtherAdmins(MovieAddedEvent $movieAddedEvent): void
    {
        $allAdmins = $this->userRepository->listAllAdmins();

        $currentUser = $movieAddedEvent->user;
        $toNotify = $allAdmins;

        if ($this->security->isGranted('ROLE_ADMIN')) {
            $toNotify = array_filter($allAdmins, static function (User $user) use ($currentUser): bool {
                return $user->getUserIdentifier() !== $currentUser->getUserIdentifier();
            });
        }

        dump(sprintf(
             'All other admins (%s) will be notified that "%s" just added the movie "%s (%d)"',
             implode(', ', array_map(fn (User $user): string => $user->getUsername(), $toNotify)),
             $currentUser->getUsername(),
             $movieAddedEvent->movie->getTitle(),
             $movieAddedEvent->movie->getReleasedAt()->format('Y')
         ));
    }
}
