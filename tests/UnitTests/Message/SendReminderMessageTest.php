<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Message;

use App\Message\SendReminderMessage;
use PHPUnit\Framework\TestCase;

class SendReminderMessageTest extends TestCase
{
    public function testEventId(): void
    {
        $message = new SendReminderMessage(123212);
        self::assertSame(
            123212,
            $message->getEventId(),
        );
    }
}
