<?php

declare(strict_types=1);

namespace App\Command;

use App\Event\EventService;
use App\Message\SendReminderMessage;
use App\Repository\EventRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'opencal:reminders:populate',
    description: 'Checks for reminders to send and creates the required messages.',
)]
class OpencalRemindersPopulateCommand extends Command
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly EventService $eventService,
        private readonly LoggerInterface $logger,
        private readonly MessageBusInterface $messageBus,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    /** @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->logger->info('Start populating reminders...');

        $events = $this->eventRepository->findUpcomingEvents();

        $this->logger->info(\sprintf(
            'Found %s to check for reminders.',
            \count($events),
        ));

        foreach ($events as $event) {
            $this->logger->info(\sprintf(
                'Check event #%s',
                $event->getId(),
            ));

            if (true !== $this->eventService->canSendReminders($event)) {
                continue;
            }

            $this->messageBus->dispatch(
                new SendReminderMessage($event->getId()),
            );
        }

        return Command::SUCCESS;
    }
}
