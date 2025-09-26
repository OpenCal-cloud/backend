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

namespace App\Tests\ApiTests\Traits;

trait RetrieveTokenTrait
{
    public function retrieveToken(
        string $email = 'user@example.tld',
        string $password = 'password',
    ): string {
        $client = self::createClient();

        $response = $client->request('POST', '/auth', [
            'headers' => [
                'Content-Type' => 'application/json',
                'accept'       => 'application/json',
            ],
            'json'    => [
                'email'    => $email,
                'password' => $password,
            ],
        ]);

        $json = $response->toArray();
        self::assertResponseIsSuccessful();
        self::assertArrayHasKey('token', $json);

        $token = $json['token'];

        self::assertIsString($token);

        return $token;
    }
}
