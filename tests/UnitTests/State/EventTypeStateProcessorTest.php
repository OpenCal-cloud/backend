<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\State;

use ApiPlatform\Metadata\Operation;
use App\Entity\EventType;
use App\MeetingProvider\AbstractMeetingProvider;
use App\MeetingProvider\MeetingProviderService;
use App\State\EventTypeStateProcessor;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use function Safe\json_encode;

class EventTypeStateProcessorTest extends TestCase
{
    private MeetingProviderService&MockObject $meetingProviderServiceMock;
    private EntityManagerInterface&MockObject $entityManagerMock;
    private Operation&MockObject $operationMock;
    private Request&MockObject $requestMock;

    protected function setUp(): void
    {
        $this->meetingProviderServiceMock = $this->createMock(MeetingProviderService::class);
        $this->entityManagerMock          = $this->createMock(EntityManagerInterface::class);
        $this->operationMock              = $this->createMock(Operation::class);
        $this->requestMock                = $this->createMock(Request::class);
    }

    public function testWithNoRequest(): void
    {
        $data = new EventType();

        $processor = $this->getProcessor();

        $result = $processor->process($data, $this->operationMock);

        self::assertSame(
            $result,
            $data,
        );
    }

    public function testWithNoIdentifiersProperty(): void
    {
        $this->requestMock
            ->method('getContent')
            ->willReturn(json_encode([]));

        $data = new EventType();

        $processor = $this->getProcessor();

        $result = $processor->process($data, $this->operationMock, [], ['request' => $this->requestMock]);

        self::assertSame(
            $result,
            $data,
        );
    }

    public function testWithEmptyIdentifiersProperty(): void
    {
        $this->requestMock
            ->method('getContent')
            ->willReturn(json_encode([
                'meetingProviderIdentifiers' => [],
            ]));

        $data = new EventType();

        $processor = $this->getProcessor();

        $result = $processor->process($data, $this->operationMock, [], ['request' => $this->requestMock]);

        self::assertSame(
            $result,
            $data,
        );
    }

    public function testWithExistingIdentifiers(): void
    {
        $this->meetingProviderServiceMock
            ->expects(self::exactly(2))
            ->method('getProviderByIdentifier')
            ->willReturn($this->createMock(AbstractMeetingProvider::class));

        $this->requestMock
            ->method('getContent')
            ->willReturn(json_encode([
                'meetingProviderIdentifiers' => [
                    'provider_1',
                    'provider_2',
                ],
            ]));

        $data = new EventType();

        $processor = $this->getProcessor();

        $result = $processor->process($data, $this->operationMock, [], ['request' => $this->requestMock]);

        self::assertSame(
            $result,
            $data,
        );
        self::assertCount(2, $data->getEventTypeMeetingProviders());
    }

    public function testWithNotExistingIdentifiers(): void
    {
        $this->meetingProviderServiceMock
            ->expects(self::exactly(1))
            ->method('getProviderByIdentifier')
            ->willReturn(null);

        $this->requestMock
            ->method('getContent')
            ->willReturn(json_encode([
                'meetingProviderIdentifiers' => [
                    'not_existing',
                ],
            ]));

        $data = new EventType();

        $processor = $this->getProcessor();

        $result = $processor->process($data, $this->operationMock, [], ['request' => $this->requestMock]);

        self::assertSame(
            $result,
            $data,
        );
        self::assertCount(0, $data->getEventTypeMeetingProviders());
    }

    private function getProcessor(): EventTypeStateProcessor
    {
        return new EventTypeStateProcessor(
            $this->meetingProviderServiceMock,
            $this->entityManagerMock,
        );
    }
}
