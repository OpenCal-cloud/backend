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

namespace App\Tests\ApiTests\Availability;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Availability;
use App\Tests\ApiTests\Traits\RetrieveTokenTrait;
use Doctrine\ORM\EntityManager;
use Spatie\Snapshots\MatchesSnapshots;
use Symfony\Component\HttpFoundation\Response;

class AvailabilitiesTest extends ApiTestCase
{
    use MatchesSnapshots;
    use RetrieveTokenTrait;

    public function testGetAvailabilitiesAsUser1(): void
    {
        $client = static::createClient();

        $token = $this->retrieveToken();

        $response = $client->request('GET', '/availabilities', [
            'auth_bearer' => $token,
            'headers'     => [
                'accept' => 'application/json',
            ],
        ]);

        $json = $response->toArray();

        self::assertMatchesJsonSnapshot($json);
        self::assertResponseIsSuccessful();
    }

    public function testGetAvailabilitiesAsUser2(): void
    {
        $client = static::createClient();

        $token = $this->retrieveToken('emily.wilson@example.tld');

        $response = $client->request('GET', '/availabilities', [
            'auth_bearer' => $token,
            'headers'     => [
                'accept' => 'application/json',
            ],
        ]);

        $json = $response->toArray();

        self::assertMatchesJsonSnapshot($json);
        self::assertResponseIsSuccessful();
    }

    public function testGetAvailabilitiesNotAuth(): void
    {
        $client = static::createClient();

        $response = $client->request('GET', '/availabilities', [
            'headers' => [
                'accept' => 'application/json',
            ],
        ]);

        self::assertSame(
            401,
            $response->getStatusCode(),
        );
    }

    public function testGetONeAvailabilityAsUser1(): void
    {
        $client = static::createClient();

        $token = $this->retrieveToken();

        $response = $client->request('GET', '/availabilities/1', [
            'auth_bearer' => $token,
            'headers'     => [
                'accept' => 'application/json',
            ],
        ]);

        $json = $response->toArray();

        self::assertMatchesJsonSnapshot($json);
        self::assertResponseIsSuccessful();
    }

    public function testGetONeAvailabilityAsUser2(): void
    {
        $client = static::createClient();

        $token = $this->retrieveToken('emily.wilson@example.tld');

        $response = $client->request('GET', '/availabilities/1', [
            'auth_bearer' => $token,
            'headers'     => [
                'accept' => 'application/json',
            ],
        ]);

        self::assertSame(
            404,
            $response->getStatusCode(),
        );
    }

    public function testGetONeAvailabilityAsUserNoAuth(): void
    {
        $client = static::createClient();

        $response = $client->request('GET', '/availabilities/1', [
            'headers' => [
                'accept' => 'application/json',
            ],
        ]);

        self::assertSame(
            401,
            $response->getStatusCode(),
        );
    }

    public function testPostAvailability(): void
    {
        $client = static::createClient();

        $token = $this->retrieveToken();

        $response = $client->request('POST', '/availabilities', [
            'auth_bearer' => $token,
            'headers'     => [
                'accept' => 'application/json',
            ],
            'json'        => [
                'dayOfWeek' => 'monday',
                'startTime' => '10:00',
                'endTime'   => '12:00',
            ],
        ]);

        self::assertSame(
            Response::HTTP_CREATED,
            $response->getStatusCode(),
        );

        $json = $response->toArray();
        $id   = $json['id'];
        self::assertIsInt($json['id']);
        unset($json['id']);
        self::assertMatchesJsonSnapshot($json);

        /** @var EntityManager $em */
        $em   = self::getContainer()->get('doctrine.orm.entity_manager');
        $repo = $em->getRepository(Availability::class);

        /** @var Availability $fetchedFromDB */
        $fetchedFromDB = $repo->find($id);
        $user          = $fetchedFromDB->getUser();

        self::assertSame(
            'user@example.tld',
            $user->getEmail(),
        );
    }

    public function testDeleteAvailability(): void
    {
        $client = static::createClient();

        $token = $this->retrieveToken();

        $response = $client->request('POST', '/availabilities', [
            'auth_bearer' => $token,
            'headers'     => [
                'accept' => 'application/json',
            ],
            'json'        => [
                'dayOfWeek' => 'monday',
                'startTime' => '10:00',
                'endTime'   => '12:00',
            ],
        ]);

        self::assertSame(
            Response::HTTP_CREATED,
            $response->getStatusCode(),
        );

        /** @var array{id: string} $json */
        $json = $response->toArray();
        $id   = $json['id'];

        $response = $client->request('DELETE', "/availabilities/{$id}", [
            'auth_bearer' => $token,
            'headers'     => [
                'accept' => 'application/json',
            ],
        ]);

        self::assertSame(
            204,
            $response->getStatusCode(),
        );
    }

    public function testPatchAvailability(): void
    {
        $client = static::createClient();

        $token = $this->retrieveToken();

        $response = $client->request('POST', '/availabilities', [
            'auth_bearer' => $token,
            'headers'     => [
                'accept' => 'application/json',
            ],
            'json'        => [
                'dayOfWeek' => 'monday',
                'startTime' => '10:00',
                'endTime'   => '12:00',
            ],
        ]);

        self::assertSame(
            Response::HTTP_CREATED,
            $response->getStatusCode(),
        );

        /** @var array{id: string} $json */
        $json = $response->toArray();
        $id   = $json['id'];

        $response = $client->request('PATCH', "/availabilities/{$id}", [
            'auth_bearer' => $token,
            'headers'     => [
                'accept'       => 'application/json',
                'content-type' => 'application/merge-patch+json',
            ],
            'json'        => [
                'dayOfWeek' => 'friday',
            ],
        ]);

        self::assertSame(
            200,
            $response->getStatusCode(),
        );

        $json = $response->toArray();
        self::assertIsInt($json['id']);
        unset($json['id']);
        self::assertMatchesJsonSnapshot($json);
    }
}
