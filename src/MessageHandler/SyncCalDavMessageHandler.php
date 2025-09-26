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

use App\Message\SyncCalDavCalendarMessage;
use App\Message\SyncCalDavMessage;
use App\Repository\CalDavAuthRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final class SyncCalDavMessageHandler
{
    public function __construct(
        private readonly CalDavAuthRepository $calDavAuthRepository,
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    /** @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter */
    public function __invoke(SyncCalDavMessage $message): void
    {
        $calDavAuths = $this->calDavAuthRepository
            ->findBy([
                'enabled' => true,
            ]);

        if (0 === \count($calDavAuths)) {
            return;
        }

        foreach ($calDavAuths as $calDavAuth) {
            $this->messageBus->dispatch(
                new SyncCalDavCalendarMessage($calDavAuth->getId()),
            );
        }
    }
}
