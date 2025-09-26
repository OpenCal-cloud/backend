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

namespace App\Tests\UnitTests\EventListener\Doctrine;

use App\Entity\Event;
use App\EventListener\Doctrine\EventPrePersistEventListener;
use PHPUnit\Framework\TestCase;

class EventPrePersistEventListenerTest extends TestCase
{
    public function testPrePersist(): void
    {
        $handler = new EventPrePersistEventListener();

        $eventMock = $this->createMock(Event::class);
        $eventMock
            ->expects($this->once())
            ->method('setCancellationHash');

        $handler->prePersist($eventMock);
    }
}
