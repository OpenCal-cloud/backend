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

namespace App\Tests\IntegrationTests\CalDAVSync;

use App\CalDav\ClientFactory;
use App\Command\SyncCalDavCommand;
use App\Entity\Event;
use App\Message\SyncCalDavMessage;
use Sabre\DAV\Client as SabreDAVClient;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Zenstruck\Messenger\Test\InteractsWithMessenger;
use function Safe\file_get_contents;

class CalDavSyncViaMessengerTest extends KernelTestCase
{
    use InteractsWithMessenger;

    public function testSyncSucceeds(): void
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $container = static::getContainer();

        $davClientMock = $this->createMock(SabreDAVClient::class);
        $davClientMock
            ->method('request')
            ->willReturn([
                'body'       => file_get_contents(__DIR__ . '/caldav_server_response_mock.xml'),
                'statusCode' => 200,
                'headers'    => [],
            ]);

        $clientFactoryMock = $this->createMock(ClientFactory::class);
        $clientFactoryMock
            ->method('getClient')
            ->willReturn($davClientMock);

        $container->set(ClientFactory::class, $clientFactoryMock);

        $messageBus = $container->get('messenger.bus.default');
        $em         = $container->get('doctrine.orm.entity_manager');

        $eventRepo = $em->getRepository(Event::class);

        $eventsBeforeSync = $eventRepo->findAll();
        self::assertCount(
            3,
            $eventsBeforeSync,
        );

        $em->clear();

        $application = new Application();
        $application->add(new SyncCalDavCommand($messageBus));

        $command       = $application->find('opencal:sync:caldav');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $output = $commandTester->getDisplay();

        self::assertStringContainsString(
            'Trigger calendar sync at',
            $output,
        );

        $this->transport()->queue()->assertCount(1);
        $this->transport()->queue()->assertContains(SyncCalDavMessage::class);
        $this->transport()->throwExceptions();
        $this->transport()->processOrFail();

        $eventsBeforeSync = $eventRepo->findAll();
        self::assertCount(
            243,
            $eventsBeforeSync,
        );
    }
}
