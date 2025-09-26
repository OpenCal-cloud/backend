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

namespace App\Tests\UnitTests\Mail;

use App\Mail\MailService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\MailerInterface;

class MailServiceTest extends TestCase
{
    private MailerInterface&MockObject $mailerMock;

    protected function setUp(): void
    {
        $this->mailerMock = $this->createMock(MailerInterface::class);
    }

    public function testSendEmailWithoutAttachments(): void
    {
        $this->mailerMock
            ->expects(self::once())
            ->method('send');

        $service = $this->getService();
        $service->sendEmail(
            'Test email',
            'Test message',
            'recipient@test.tld',
            'Testing subject',
        );
    }

    public function testSendEmailWithAttachments(): void
    {
        $this->mailerMock
            ->expects(self::once())
            ->method('send');

        $service = $this->getService();
        $service->sendEmail(
            'Test email',
            'Test message',
            'recipient@test.tld',
            'Testing subject',
            [
                __FILE__ => 'Test.php',
            ],
        );
    }

    private function getService(): MailService
    {
        return new MailService(
            $this->mailerMock,
            'sender@test.tld',
            'Unit Test',
        );
    }
}
