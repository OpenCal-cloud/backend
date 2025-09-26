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

namespace App\CalDav;

use App\Entity\CalDavAuth;
use App\Entity\CalDavSyncLog;
use Doctrine\ORM\EntityManagerInterface;
use Safe\DateTimeImmutable;

class LogService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function createLogEntry(CalDavAuth $calDavAuth): CalDavSyncLog
    {
        $logEntry = new CalDavSyncLog();
        $logEntry
            ->setCreatedAt(new DateTimeImmutable())
            ->setCalDavAuth($calDavAuth);

        return $logEntry;
    }

    public function saveLogEntry(CalDavSyncLog $logEntry): void
    {
        $this->entityManager->persist($logEntry);
        $this->entityManager->flush();
    }
}
