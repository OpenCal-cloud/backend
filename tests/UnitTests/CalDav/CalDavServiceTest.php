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

namespace App\Tests\UnitTests\CalDav;

use App\CalDav\CalDavService;
use App\CalDav\ClientFactory;
use App\Entity\CalDavAuth;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sabre\DAV\Client;
use Spatie\Snapshots\MatchesSnapshots;
use function Safe\file_get_contents;

class CalDavServiceTest extends TestCase
{
    use MatchesSnapshots;

    private ClientFactory&MockObject $clientFactoryMock;
    private Client&MockObject $clientMock;

    protected function setUp(): void
    {
        $this->clientFactoryMock = $this->createMock(ClientFactory::class);
        $this->clientMock        = $this->createMock(Client::class);

        $this->clientFactoryMock
            ->method('getClient')
            ->willReturn($this->clientMock);
    }

    public function testFetchEventsByAuthWithEmptyResponse(): void
    {
        $this->clientMock
            ->method('request')
            ->willReturn([
                'body' => <<<EOF
<?xml version='1.0' encoding='utf-8'?>
<multistatus xmlns="DAV:" />
EOF,
            ]);

        $authMock = new CalDavAuth();
        $authMock
            ->setBaseUri('https://url.tld')
            ->setUsername('test')
            ->setPassword('test');

        $service = $this->getService();

        $result = $service->fetchEventsByAuth($authMock);

        self::assertSame([], $result);
    }

    public function testFetchEventsByAuthWithNullResponse(): void
    {
        $this->clientMock
            ->method('request')
            ->willReturn(null);

        $authMock = new CalDavAuth();
        $authMock
            ->setBaseUri('https://url.tld')
            ->setUsername('test')
            ->setPassword('test');

        $service = $this->getService();

        $result = $service->fetchEventsByAuth($authMock);

        self::assertSame([], $result);
    }

    public function testFetchEventsByAuthWithEvents(): void
    {
        $this->clientMock
            ->method('request')
            ->willReturn([
                'status' => 207,
                'body'   => file_get_contents(__DIR__ . '/responseMock.xml'),
            ]);

        $authMock = new CalDavAuth();
        $authMock
            ->setBaseUri('https://url.tld')
            ->setUsername('test')
            ->setPassword('test');

        $service = $this->getService();

        $result = $service->fetchEventsByAuth($authMock);

        self::assertMatchesJsonSnapshot($result);
    }

    public function testBuildRequestXML(): void
    {
        $service = $this->getService();

        $refClass = new \ReflectionClass($service);
        $method   = $refClass->getMethod('buildRequestXML');

        $xml = $method->invokeArgs($service, [
            'startDateString' => '01.01.2025 00:00:00',
        ]);

        self::assertSame(
            '<c:calendar-query xmlns:d="DAV:" xmlns:c="urn:ietf:params:xml:ns:caldav">
    <d:prop>
        <d:getetag />
        <c:calendar-data />
    </d:prop>
    <c:filter>
        <c:comp-filter name="VCALENDAR">
          <c:comp-filter name="VEVENT">
            <c:time-range start="20250101T000000Z" end="20250401T000000Z"/>
          </c:comp-filter>
        </c:comp-filter>
    </c:filter>
</c:calendar-query>',
            $xml,
        );
    }

    public function testGetClient(): void
    {
        $service = $this->getService();

        $refClass = new \ReflectionClass($service);
        $method   = $refClass->getMethod('getClient');

        $client1 = $method->invokeArgs($service, [
            'baseUri'  => 'http://caldav.domain1.tld',
            'username' => 'test',
            'password' => 'test',
        ]);
        self::assertInstanceOf(
            Client::class,
            $client1,
        );

        $client2 = $method->invokeArgs($service, [
            'baseUri'  => 'http://caldav.domain2.tld',
            'username' => 'test',
            'password' => 'test',
        ]);
        self::assertInstanceOf(
            Client::class,
            $client2,
        );
    }

    private function getService(): CalDavService
    {
        return new CalDavService(
            $this->clientFactoryMock,
        );
    }
}
