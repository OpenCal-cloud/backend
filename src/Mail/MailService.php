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

namespace App\Mail;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class MailService
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly string $emailSenderAddress,
        private readonly string $emailSenderName,
    ) {
    }

    /**
     * @param array<string, string> $attachments
     *
     * @throws TransportExceptionInterface
     */
    public function sendEmail(
        string $subject,
        string $message,
        string $recipientEmail,
        string $recipientName,
        array $attachments = [],
    ): void {
        $email = (new Email())
            ->from(new Address($this->emailSenderAddress, $this->emailSenderName))
            ->to(new Address($recipientEmail, $recipientName))
            ->subject($subject)
            ->text($message);

        foreach ($attachments as $filePath => $attachmentName) {
            $email->attachFromPath($filePath, $attachmentName);
        }

        $this->mailer->send($email);
    }
}
