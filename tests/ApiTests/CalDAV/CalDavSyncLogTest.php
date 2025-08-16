<?php

declare(strict_types=1);

namespace App\Tests\ApiTests\CalDAV;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\ApiTests\Traits\RetrieveTokenTrait;
use Spatie\Snapshots\MatchesSnapshots;

class CalDavSyncLogTest extends ApiTestCase
{
    use MatchesSnapshots;
    use RetrieveTokenTrait;

    public function testFetchAllCalDavAuthsCurrentUser(): void
    {
        $client = static::createClient();

        $token = $this->retrieveToken();

        $response = $client->request('GET', '/cal_dav_sync_logs', [
            'auth_bearer' => $token,
            'headers'     => [
                'accept' => 'application/json',
            ],
        ]);

        $json = $response->toArray();

        self::assertMatchesJsonSnapshot($json);
        self::assertResponseIsSuccessful();
    }

    public function testFetchOnlyFailed(): void
    {
        $client = static::createClient();

        $token = $this->retrieveToken();

        $response = $client->request('GET', '/cal_dav_sync_logs?failed=true', [
            'auth_bearer' => $token,
            'headers'     => [
                'accept' => 'application/json',
            ],
        ]);

        $json = $response->toArray();

        self::assertMatchesJsonSnapshot($json);
        self::assertResponseIsSuccessful();
    }

    public function testFetchOnlyNotFailed(): void
    {
        $client = static::createClient();

        $token = $this->retrieveToken();

        $response = $client->request('GET', '/cal_dav_sync_logs?failed=false', [
            'auth_bearer' => $token,
            'headers'     => [
                'accept' => 'application/json',
            ],
        ]);

        $json = $response->toArray();

        self::assertMatchesJsonSnapshot($json);
        self::assertResponseIsSuccessful();
    }

    public function testFetchOtherCalDavAuthNoResults(): void
    {
        $client = static::createClient();

        $token = $this->retrieveToken();

        $response = $client->request('GET', '/cal_dav_sync_logs?calDavAuth.id=2', [
            'auth_bearer' => $token,
            'headers'     => [
                'accept' => 'application/json',
            ],
        ]);

        $json = $response->toArray();

        self::assertMatchesJsonSnapshot($json);
        self::assertResponseIsSuccessful();
    }
}
