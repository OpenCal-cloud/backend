<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\CalDavAuth;
use App\Entity\CalDavSyncLog;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Safe\DateTimeImmutable;

class CalDavAuthFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $user1       = $this->getReference('user1', User::class);
        $calDavAuth1 = new CalDavAuth();
        $calDavAuth1
            ->setEnabled(true)
            ->setUsername('dev')
            ->setPassword('dev')
            ->setBaseUri('http://radicale:5232/dev/example/')
            ->setUser($user1);
        $manager->persist($calDavAuth1);
        $this->createLogEntries($manager, $calDavAuth1);

        $user2       = $this->getReference('user2', User::class);
        $calDavAuth2 = new CalDavAuth();
        $calDavAuth2
            ->setEnabled(true)
            ->setUsername('user2')
            ->setPassword('password')
            ->setBaseUri('http://caldav.calendar')
            ->setUser($user2);
        $manager->persist($calDavAuth2);
        $this->createLogEntries($manager, $calDavAuth2);

        $manager->flush();
    }

    private function createLogEntries(ObjectManager $manager, CalDavAuth $calDavAuth): void
    {
        for ($i = 0; $i < 10; $i++) {
            $failed = \boolval($i % 2);

            $entry = new CalDavSyncLog();
            $entry
                ->setCreatedAt(new DateTimeImmutable($i . '-01-2024'))
                ->setCalDavAuth($calDavAuth)
                ->setCountItems(3 * $i)
                ->setFailed($failed)
                ->setErrorMessage($failed ? 'CalDAV-Sync has failed.' : null)
                ->setErrorMessage($failed ? 'This is a example stack trace...' : null);

            $manager->persist($entry);
        }

        $manager->flush();
    }
}
