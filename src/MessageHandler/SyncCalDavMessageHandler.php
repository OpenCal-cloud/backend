<?php

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
