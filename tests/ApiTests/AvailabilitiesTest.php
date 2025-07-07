<?php

declare(strict_types=1);

namespace App\Tests\ApiTests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\ApiTests\Traits\RetrieveTokenTrait;
use Spatie\Snapshots\MatchesSnapshots;

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
}
