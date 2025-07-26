<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\ApiResource;

use App\ApiResource\Availability;
use App\Tests\Traits\TestAttributesTrait;
use PHPUnit\Framework\TestCase;

class AvailabilityTest extends TestCase
{
    use TestAttributesTrait;

    protected function getInstance(): Availability
    {
        return new Availability();
    }
}
