<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\ApiResource;

use App\ApiResource\ResetPassword;
use App\Tests\Traits\TestAttributesTrait;
use PHPUnit\Framework\TestCase;

class ResetPasswordTest extends TestCase
{
    use TestAttributesTrait;

    public function testEmail(): void
    {
        $resource = $this->getInstance();
        $resource->setEmail('email@tld.com');

        self::assertSame(
            'email@tld.com',
            $resource->getEmail(),
        );
    }

    public function testToken(): void
    {
        $resource = $this->getInstance();
        $resource->setToken('the-token');

        self::assertSame(
            'the-token',
            $resource->getToken(),
        );
    }

    public function testPassword(): void
    {
        $resource = $this->getInstance();
        $resource->setPassword('secure-password');

        self::assertSame(
            'secure-password',
            $resource->getPassword(),
        );
    }

    protected function getInstance(): ResetPassword
    {
        return new ResetPassword();
    }
}
