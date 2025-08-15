<?php

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
