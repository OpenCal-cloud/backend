<?php

declare(strict_types=1);

namespace App\MeetingProvider;

use App\Entity\Event;

abstract class AbstractMeetingProvider
{
    abstract public function getIdentifier(): string;

    abstract public function getName(): string;

    abstract public function generateMeetingUrl(Event $event): string;

    abstract public function isAvailable(): bool;
}
