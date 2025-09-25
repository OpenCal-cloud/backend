<?php

declare(strict_types=1);

namespace App\Message;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('async')]
final class SyncCalDavCalendarMessage
{
    public function __construct(
        private readonly int $calDavAuthId,
    ) {
    }

    public function getCalDavAuthId(): int
    {
        return $this->calDavAuthId;
    }
}
