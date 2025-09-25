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

namespace App\DataFixtures;

use App\Entity\EventType;
use App\Entity\EventTypeMeetingProvider;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class EventTypeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $user1 = $this->getReference('user1', User::class);
        $user2 = $this->getReference('user2', User::class);

        $eventTypesData = [
            [
                'name'        => 'Conference Call',
                'description' => 'A call to discuss project updates.',
                'duration'    => 60,
                'slug'        => 'conference-call',
                'host'        => $user1,
            ],
            [
                'name'        => 'Team Meeting',
                'description' => 'Weekly sync with the team.',
                'duration'    => 30,
                'slug'        => 'team-meeting',
                'host'        => $user2,
            ],
            [
                'name'        => 'One-on-One',
                'description' => 'Personal meeting with a team member.',
                'duration'    => 45,
                'slug'        => 'one-on-one',
                'host'        => $user1,
            ],
        ];

        $count = 0;

        foreach ($eventTypesData as $index => $data) {
            $eventType = new EventType();
            $eventType->setName($data['name'])
                ->setDescription($data['description'])
                ->setDuration($data['duration'])
                ->setSlug($data['slug'])
                ->setHost($data['host']);

            $manager->persist($eventType);

            $this->addReference('eventType' . ($index + 1), $eventType);

            if (0 === $count) {
                $jitsiMeetingProvider = new EventTypeMeetingProvider();
                $jitsiMeetingProvider
                    ->setEventType($eventType)
                    ->setEnabled(true)
                    ->setProviderIdentifier('jitsi_meet');
                $manager->persist($jitsiMeetingProvider);

                $phoneMeetingProvider = new EventTypeMeetingProvider();
                $phoneMeetingProvider
                    ->setEventType($eventType)
                    ->setEnabled(true)
                    ->setProviderIdentifier('phone');
                $manager->persist($phoneMeetingProvider);
            }

            $count++;
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
