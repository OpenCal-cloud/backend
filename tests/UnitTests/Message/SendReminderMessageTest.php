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
