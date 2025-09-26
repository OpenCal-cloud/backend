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
use App\Message\EventCanceledMessage;
use App\Notification\Email\BookingCanceledToHostEmailNotificationService;
use App\Repository\EventRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class EventCanceledMessageHandler
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly BookingCanceledToHostEmailNotificationService $bookingCanceledToHostEmailNotificationService,
    ) {
    }

    public function __invoke(EventCanceledMessage $message): void
    {
        $event = $this->eventRepository->find($message->getEventId());

        if (!$event instanceof Event) {
            return;
        }

        $this->bookingCanceledToHostEmailNotificationService->sendNotification($event);
    }
}
