<?php

declare(strict_types=1);

namespace App\Tests\ApiTests\Availability;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\ApiTests\Traits\RetrieveTokenTrait;
use Spatie\Snapshots\MatchesSnapshots;

class UnavailabilityTest extends ApiTestCase
{
    use MatchesSnapshots;
    use RetrieveTokenTrait;

    public function testGetCollection(): void
    {
        $responseData = $this->fetchCollection();
        self::assertMatchesJsonSnapshot($responseData);
    }

    public function testGetItem(): void
    {
        $client = static::createClient();

        $token = $this->retrieveToken();

        $response = $client->request('GET', '/unavailabilities/1', [
            'auth_bearer' => $token,
            'headers'     => [
                'accept' => 'application/json',
            ],
        ]);

        $json = $response->toArray();

        self::assertMatchesJsonSnapshot($json);
        self::assertResponseIsSuccessful();
    }

    public function testPost(): void
    {
        $client = static::createClient();

        $token = $this->retrieveToken();

        $response = $client->request('POST', '/unavailabilities', [
            'auth_bearer' => $token,
            'headers'     => [
                'accept' => 'application/json',
            ],
            'json'        => [
                'dayOfWeek' => 'monday',
                'fullDay'   => true,
            ],
        ]);

        $json = $response->toArray();

        self::assertMatchesJsonSnapshot($json);
        self::assertResponseIsSuccessful();

        $responseData = $this->fetchCollection();
        self::assertMatchesJsonSnapshot($responseData);
    }

    public function testPatch(): void
    {
        $client = static::createClient();

        $token = $this->retrieveToken();

        $response = $client->request('PATCH', '/unavailabilities/2', [
            'auth_bearer' => $token,
            'headers'     => [
                'accept'       => 'application/json',
                'content-type' => 'application/merge-patch+json',
            ],
            'json'        => [
                'dayOfWeek' => 'monday',
                'startTime' => '2025-01-13T13:00:00.000Z',
                'endTime'   => '2025-01-13T17:00:00.000Z',
                'fullDay'   => false,
            ],
        ]);

        $json = $response->toArray();

        self::assertMatchesJsonSnapshot($json);
        self::assertResponseIsSuccessful();

        $responseData = $this->fetchCollection();
        self::assertMatchesJsonSnapshot($responseData);
    }

    public function testDelete(): void
    {
        $client = static::createClient();

        $token = $this->retrieveToken();

        $client->request('DELETE', '/unavailabilities/2', [
            'auth_bearer' => $token,
            'headers'     => [
                'accept' => 'application/json',
            ],
        ]);

        self::assertResponseIsSuccessful();

        $responseData = $this->fetchCollection();
        self::assertMatchesJsonSnapshot($responseData);
    }

    /** @return array<mixed> */
    private function fetchCollection(): array
    {
        $client = static::createClient();

        $token = $this->retrieveToken();

        $response = $client->request('GET', '/unavailabilities', [
            'auth_bearer' => $token,
            'headers'     => [
                'accept' => 'application/json',
            ],
        ]);

        /** @var array<mixed> $json */
        $json = $response->toArray();

        self::assertResponseIsSuccessful();

        return $json;
    }
}
