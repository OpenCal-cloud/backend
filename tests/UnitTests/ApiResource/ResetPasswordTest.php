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
