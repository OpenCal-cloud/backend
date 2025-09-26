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

namespace App\MessageHandler;

use App\Entity\Event;
use App\Message\NewBookingMessage;
use App\Notification\Email\NewBookingToAttendeeEmailNotificationService;
use App\Notification\Email\NewBookingToHostEmailNotificationService;
use App\Repository\EventRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class NewBookingMessageHandler
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly NewBookingToHostEmailNotificationService $newBookingToHostEmailNotificationService,
        private readonly NewBookingToAttendeeEmailNotificationService $newBookingToAttendeeEmailNotificationService,
    ) {
    }

    public function __invoke(NewBookingMessage $message): void
    {
        $event = $this->eventRepository->find($message->getEventId());

        if (!$event instanceof Event || null !== $event->getSyncHash()) {
            return;
        }

        $this->newBookingToHostEmailNotificationService->sendNotification($event);
        $this->newBookingToAttendeeEmailNotificationService->sendNotification($event);
    }
}
