<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\MessageHandler;

use App\CalDav\CalDavService;
use App\CalDav\LogService;
use App\Entity\CalDavAuth;
use App\Entity\CalDavSyncLog;
use App\Message\SyncCalDavCalendarMessage;
use App\MessageHandler\SyncCalDavCalendarMessageHandler;
use App\Repository\CalDavAuthRepository;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class SyncCalDavCalendarMessageHandlerTest extends TestCase
{
    private CalDavService&MockObject $calDavServiceMock;
    private CalDavAuthRepository&MockObject $calDavAuthRepositoryMock;
    private EventRepository&MockObject $eventRepositoryMock;
    private EntityManagerInterface&MockObject $entityManagerMock;
    private LoggerInterface&MockObject $loggerMock;
    private LogService&MockObject $logServiceMock;

    protected function setUp(): void
    {
        $this->calDavServiceMock        = $this->createMock(CalDavService::class);
        $this->calDavAuthRepositoryMock = $this->createMock(CalDavAuthRepository::class);
        $this->eventRepositoryMock      = $this->createMock(EventRepository::class);
        $this->entityManagerMock        = $this->createMock(EntityManagerInterface::class);
        $this->loggerMock               = $this->createMock(LoggerInterface::class);
        $this->logServiceMock           = $this->createMock(LogService::class);
    }

    public function testInvokeWithoutAuths(): void
    {
        $this->logServiceMock
            ->expects(self::never())
            ->method('saveLogEntry');

        $this->calDavAuthRepositoryMock
            ->method('findBy')
            ->willReturn([]);

        $this->entityManagerMock
            ->expects($this->never())
            ->method('persist');
        $this->entityManagerMock
            ->expects($this->never())
            ->method('flush');

        $handler = $this->getHandler();

        $handler->__invoke(new SyncCalDavCalendarMessage(1));
    }

    public function testInvokeWithAuthsAndEventData(): void
    {
        $this->logServiceMock
            ->expects(self::exactly(1))
            ->method('saveLogEntry');

        $authMock1 = $this->createMock(CalDavAuth::class);
        $authMock1
            ->expects(self::once())
            ->method('setSyncedAt');

        $this->calDavAuthRepositoryMock
            ->method('find')
            ->willReturn($authMock1);

        $this->entityManagerMock
            ->expects($this->exactly(3))
            ->method('persist');

        $this->calDavServiceMock
            ->method('fetchEventsByAuth')
            ->willReturn([
                [
                    'day'       => '2021-01-01',
                    'startTime' => '10:00',
                    'endTime'   => '11:00',
                    'etag'      => '123hash',
                ],
                [
                    'day'       => '2021-01-01',
                    'startTime' => '10:00',
                    'endTime'   => '11:00',
                    'etag'      => '123hash',
                ],
            ]);

        $handler = $this->getHandler();

        $handler->__invoke(new SyncCalDavCalendarMessage(1));
    }

    public function testInvokeThrowsSyncException(): void
    {
        $logEntryMock = $this->createMock(CalDavSyncLog::class);
        $logEntryMock
            ->method('setFailed')
            ->with(true);

        $this->logServiceMock
            ->method('createLogEntry')
            ->willReturn($logEntryMock);

        $this->logServiceMock
            ->expects(self::atLeastOnce())
            ->method('saveLogEntry');

        $this->calDavServiceMock
            ->method('fetchEventsByAuth')
            ->willThrowException(new \Exception('test'));

        $this->calDavAuthRepositoryMock
            ->method('find')
            ->willReturn($this->createMock(CalDavAuth::class));

        $handler = $this->getHandler();
        $handler->__invoke(new SyncCalDavCalendarMessage(1));
    }

    private function getHandler(): SyncCalDavCalendarMessageHandler
    {
        return new SyncCalDavCalendarMessageHandler(
            $this->calDavServiceMock,
            $this->eventRepositoryMock,
            $this->entityManagerMock,
            $this->loggerMock,
            $this->logServiceMock,
            $this->calDavAuthRepositoryMock,
        );
    }
}
