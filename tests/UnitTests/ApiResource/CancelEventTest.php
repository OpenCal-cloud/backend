<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\ApiResource;

use App\ApiResource\CancelEvent;
use App\Tests\Traits\TestAttributesTrait;
use PHPUnit\Framework\TestCase;

class CancelEventTest extends TestCase
{
    use TestAttributesTrait;

    public function testCancellationHash(): void
    {
        $resource = $this->getInstance();
        $resource->setCancellationHash('hash-1234');

        self::assertSame(
            'hash-1234',
            $resource->getCancellationHash(),
        );
    }

    protected function getInstance(): CancelEvent
    {
        return new CancelEvent();
    }
}
