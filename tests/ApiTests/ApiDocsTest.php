<?php

declare(strict_types=1);

namespace App\Tests\ApiTests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiDocsTest extends WebTestCase
{
    public function testApiDocs(): void
    {
        $client = self::createClient();

        $client->request('GET', '/');

        self::assertResponseIsSuccessful();
    }
}
