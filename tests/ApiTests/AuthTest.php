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
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class AuthTest extends ApiTestCase
{
    public function testNotAuthenticated(): void
    {
        $client = static::createClient();

        $client->request('GET', '/me', [
            'headers' => [
                'accept' => 'application/json',
            ],
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testAuthWithDisabledUser(): void
    {
        $client    = self::createClient();
        $container = self::getContainer();

        /** @var EntityManager $em */
        $em = $container->get(EntityManagerInterface::class);

        $email    = 'disabled@test.tld';
        $password = 'not-required';
        $user     = new User();
        $user
            ->setGivenName('Test')
            ->setFamilyName('Disabled')
            ->setEnabled(false)
            ->setEmail($email)
            ->setPassword($password);
        $em->persist($user);
        $em->flush();
        $em->clear();

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

        self::assertSame(
            $response->getStatusCode(),
            Response::HTTP_UNAUTHORIZED,
        );
    }
}
