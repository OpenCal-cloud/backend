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

use App\Entity\User;
use App\Message\PasswordRequestedMessage;
use App\Repository\UserRepository;
use App\User\UserMailService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class PasswordRequestedMessageHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private UserMailService $userMailService,
    ) {
    }

    public function __invoke(PasswordRequestedMessage $message): void
    {
        $user = $this->userRepository->find($message->getUserId());

        if (!$user instanceof User) {
            return;
        }

        $this->userMailService->sendPasswordResetEmail($user);
    }
}
