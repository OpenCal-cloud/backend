<?php
/*
 * Copyright (c) 2025. All Rights Reserved.
 *
 * This file is part of the OpenCal project, see https://git.var-lab.com/opencal
 *
 * You may use, distribute and modify this code under the terms of the AGPL 3.0 license,
 * which unfortunately won't be written for another century.
 *
 * Visit https://git.var-lab.com/opencal/backend/-/blob/main/LICENSE to read the full license text.
 */

declare(strict_types=1);

namespace App\EventListener\Doctrine;

use App\Entity\CalDavAuth;
use App\Entity\Event;
use App\MeetingProvider\AbstractMeetingProvider;
use App\MeetingProvider\MeetingProviderService;
use App\Message\NewBookingMessage;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: Event::class)]
class EventPostPersistEventListener
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
        private readonly MeetingProviderService $meetingProviderService,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function postPersist(Event $event): void
    {
        if (null === $event->getParticipationUrl() && null !== $event->getMeetingProviderIdentifier()) {
            $meetingProvider = $this->meetingProviderService
                ->getProviderByIdentifier($event->getMeetingProviderIdentifier());

            if ($meetingProvider instanceof AbstractMeetingProvider) {
                $participationUrl = $meetingProvider->generateMeetingUrl($event);

                $event->setParticipationUrl($participationUrl);
            }

            $this->entityManager->persist($event);
            $this->entityManager->flush();
        }

        if ($event->getCalDavAuth() instanceof CalDavAuth) {
            return;
        }

        $this->messageBus->dispatch(
            new NewBookingMessage($event->getId()),
        );
    }
}
