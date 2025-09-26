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

use App\Entity\EventTypeMeetingProvider;
use App\MeetingProvider\AbstractMeetingProvider;
use App\MeetingProvider\MeetingProviderService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::postLoad, method: 'postLoad', entity: EventTypeMeetingProvider::class)]
class EventTypeMeetingProviderPostLoadListener
{
    public function __construct(
        private readonly MeetingProviderService $meetingProviderService,
    ) {
    }

    public function postLoad(EventTypeMeetingProvider $eventTypeMeetingProvider): void
    {
        $provider = $this->meetingProviderService
            ->getProviderByIdentifier($eventTypeMeetingProvider->getProviderIdentifier());

        if (!$provider instanceof AbstractMeetingProvider) {
            throw new \RuntimeException(\sprintf(
                'No meeting provider found by identifier %s.',
                $eventTypeMeetingProvider->getProviderIdentifier(),
            ));
        }

        $eventTypeMeetingProvider
            ->setName($provider->getName());
    }
}
