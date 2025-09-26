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

namespace App\User;

use App\Entity\User;
use App\Mail\MailService;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserMailService
{
    public function __construct(
        private readonly MailService $mailService,
        private readonly TranslatorInterface $translator,
        private readonly string $locale,
        private readonly string $frontendDomain,
        private readonly bool $useSSL,
    ) {
    }

    public function sendPasswordResetEmail(User $user): void
    {
        $params = [
            '{user_name}' => $user->getGivenName(),
            '{reset_url}' => \sprintf(
                '%s/password/reset/%s/%s',
                $this->getFrontendUrl(),
                $user->getPasswordResetToken(),
                $user->getEmail(),
            ),
        ];

        $this->mailService->sendEmail(
            $this->translator->trans('mails.password_request.subject', [], 'messages', $this->locale),
            $this->translator->trans('mails.password_request.message', $params, 'messages', $this->locale),
            $user->getEmail(),
            \sprintf(
                '%s %s',
                $user->getGivenName(),
                $user->getFamilyName(),
            ),
        );
    }

    protected function getFrontendUrl(): string
    {
        $protocol = $this->useSSL ? 'https' : 'http';

        return \sprintf(
            '%s://%s',
            $protocol,
            $this->frontendDomain,
        );
    }
}
