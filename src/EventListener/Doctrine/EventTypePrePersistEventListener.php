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

use App\Entity\EventType;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Bundle\SecurityBundle\Security;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: EventType::class)]
class EventTypePrePersistEventListener
{
    public function __construct(
        private readonly Security $security,
    ) {
    }

    public function prePersist(EventType $eventType): void
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return;
        }

        $eventType->setHost($user);
    }
}
