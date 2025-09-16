<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\Event;
use App\Event\EventService;
use App\Message\SendReminderMessage;
use App\Repository\EventRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SendReminderMessageHandler
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly EventService $eventService,
    ) {
    }

    public function __invoke(SendReminderMessage $message): void
    {
        $event = $this->eventRepository->find($message->getEventId());

        if (!$event instanceof Event) {
            return;
        }

        if (!$this->eventService->canSendReminders($event)) {
            return;
        }

        $this->eventService->sendReminders($event);
    }
}
