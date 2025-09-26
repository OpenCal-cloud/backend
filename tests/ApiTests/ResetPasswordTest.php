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

namespace App\Tests\ApiTests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Message\PasswordRequestedMessage;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Messenger\Test\InteractsWithMessenger;

class ResetPasswordTest extends ApiTestCase
{
    use InteractsWithMessenger;

    public function testRequestPasswordWithExistingUser(): void
    {
        self::transport()->reset();

        $client = static::createClient();

        $response = $client->request('POST', '/password/request', [
            'headers' => [
                'accept' => 'application/json',
            ],
            'json'    => [
                'email' => 'user@example.tld',
            ],
        ]);

        self::assertSame(Response::HTTP_NO_CONTENT, $response->getStatusCode());
        self::transport()->queue()->assertCount(1);
        self::transport()->queue()->assertContains(PasswordRequestedMessage::class);
    }

    public function testRequestPasswordWithUserNotFound(): void
    {
        self::transport()->reset();

        $client = static::createClient();

        $response = $client->request('POST', '/password/request', [
            'headers' => [
                'accept' => 'application/json',
            ],
            'json'    => [
                'email' => 'no-user@example.tld',
            ],
        ]);

        self::assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        self::transport()->queue()->assertCount(0);
    }
}
