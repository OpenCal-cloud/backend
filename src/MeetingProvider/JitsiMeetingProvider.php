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

namespace App\MeetingProvider;

use App\Entity\Event;
use App\Helper\SlugHelper;
use function Safe\preg_match;

class JitsiMeetingProvider extends AbstractMeetingProvider
{
    public const string PROVIDER_IDENTIFIER = 'jitsi_meet';

    public function __construct(
        private readonly string $jitsiMeetBaseUrl,
        private readonly SlugHelper $slugHelper,
    ) {
    }

    public function getIdentifier(): string
    {
        return self::PROVIDER_IDENTIFIER;
    }

    public function getName(): string
    {
        return 'Jitsi Meet';
    }

    public function generateMeetingUrl(Event $event): string
    {
        $meetingName = \sprintf(
            '%s - %s (Meeting #%s)',
            $event->getEventType()?->getName(),
            $event->getDay()->format('d.m.Y'),
            $event->getId(),
        );

        $meetingName = $this->slugHelper->slugify($meetingName);

        return \sprintf(
            '%s/%s',
            $this->jitsiMeetBaseUrl,
            $meetingName,
        );
    }

    public function isAvailable(): bool
    {
        return (bool) preg_match(
            "/\b(?:https:\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",
            $this->jitsiMeetBaseUrl,
        );
    }
}
