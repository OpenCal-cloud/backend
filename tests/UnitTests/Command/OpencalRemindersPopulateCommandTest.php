<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Command;

use App\Command\OpencalRemindersPopulateCommand;
use App\Entity\Event;
use App\Event\EventService;
use App\Message\SendReminderMessage;
use App\Repository\EventRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class OpencalRemindersPopulateCommandTest extends TestCase
{
    private EventRepository&MockObject $eventRepository;
    private EventService&MockObject $eventService;
    private LoggerInterface&MockObject $logger;
    private MessageBusInterface&MockObject $messageBus;

    protected function setUp(): void
    {
        $this->eventRepository = $this->createMock(EventRepository::class);
        $this->eventService    = $this->createMock(EventService::class);
        $this->logger          = $this->createMock(LoggerInterface::class);
        $this->messageBus      = $this->createMock(MessageBusInterface::class);
    }

    public function testConfigure(): void
    {
        $cmd = $this->getCommand();

        $arguments = $cmd->getDefinition()->getArguments();
        self::assertCount(
            0,
            $arguments,
        );

        $synopsis = $cmd->getSynopsis();
        self::assertSame(
            'opencal:reminders:populate',
            $synopsis,
        );
    }

    public function testExecuteWithNoEvents(): void
    {
        $cmd = $this->getCommand();

        $this->messageBus
            ->expects(self::never())
            ->method('dispatch');

        $refClass = new \ReflectionClass($cmd);
        $method   = $refClass->getMethod('execute');

        $method->invoke(
            $cmd,
            $this->createMock(InputInterface::class),
            $this->createMock(OutputInterface::class),
        );
    }

    public function testExecuteWithEventsCantSendReminders(): void
    {
        $events = [
            $this->createMock(Event::class),
        ];

        $this->eventRepository
            ->method('findUpcomingEvents')
            ->willReturn($events);

        $cmd = $this->getCommand();

        $this->messageBus
            ->expects(self::exactly(0))
            ->method('dispatch');

        $refClass = new \ReflectionClass($cmd);
        $method   = $refClass->getMethod('execute');

        $method->invoke(
            $cmd,
            $this->createMock(InputInterface::class),
            $this->createMock(OutputInterface::class),
        );
    }

    public function testExecuteSucceeds(): void
    {
        $eventMock1 = $this->createMock(Event::class);
        $eventMock2 = $this->createMock(Event::class);

        $events = [
            $eventMock1,
            $eventMock2,
        ];

        $this->eventRepository
            ->method('findUpcomingEvents')
            ->willReturn($events);

        $this->eventService
            ->method('canSendReminders')
            ->willReturn(true);

        $cmd = $this->getCommand();

        $this->messageBus
            ->expects(self::exactly(2))
            ->method('dispatch')
            ->willReturn(new Envelope(new SendReminderMessage(101)));

        $refClass = new \ReflectionClass($cmd);
        $method   = $refClass->getMethod('execute');

        $method->invoke(
            $cmd,
            $this->createMock(InputInterface::class),
            $this->createMock(OutputInterface::class),
        );
    }

    private function getCommand(): OpencalRemindersPopulateCommand
    {
        return new OpencalRemindersPopulateCommand(
            $this->eventRepository,
            $this->eventService,
            $this->logger,
            $this->messageBus,
        );
    }
}
