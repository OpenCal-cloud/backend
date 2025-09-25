<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\CalDav\CalDavService;
use App\CalDav\LogService;
use App\Entity\CalDavAuth;
use App\Entity\Event;
use App\Message\SyncCalDavCalendarMessage;
use App\Repository\CalDavAuthRepository;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Safe\DateTime;
use Safe\DateTimeImmutable;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SyncCalDavCalendarMessageHandler
{
    public function __construct(
        private readonly CalDavService $calDavService,
        private readonly EventRepository $eventRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger,
        private readonly LogService $logService,
        private readonly CalDavAuthRepository $calDavAuthRepository,
    ) {
    }

    public function __invoke(SyncCalDavCalendarMessage $message): void
    {
        $calDavAuth = $this->calDavAuthRepository->find($message->getCalDavAuthId());

        if (!$calDavAuth instanceof CalDavAuth) {
            return;
        }

        $this->logger->info(\sprintf(
            '[caldav sync]: Start sync for caldav-auth-id %s',
            $calDavAuth->getId(),
        ));

        $logEntry = $this->logService->createLogEntry($calDavAuth);

        $eventsData = [];

        try {
            $eventsData = $this->calDavService->fetchEventsByAuth($calDavAuth);

            $this->logger->info(\sprintf(
                '[caldav sync]: Count fetched entries: %s',
                \count($eventsData),
            ));

            $logEntry
                ->setFailed(false)
                ->setCountItems(\count($eventsData));

            $this->setSyncedAt($calDavAuth);
        } catch (\Throwable $ex) {
            $logEntry
                ->setCountItems(0)
                ->setErrorDetails($ex->getMessage())
                ->setFailed(true);
        }

        $addedETags = [];

        /** @var array{day: string, startTime: string, endTime: string, etag: string} $item */
        foreach ($eventsData as $item) {
            $event = $this->eventRepository->findOneBySyncHashAndUser($item['etag'], $calDavAuth->getUser());

            if (!$event instanceof Event) {
                $event = new Event();
            }

            $event
                ->setDay(new DateTime($item['day']))
                ->setStartTime(new DateTime($item['startTime']))
                ->setEndTime(new DateTime($item['endTime']))
                ->setSyncHash($item['etag'])
                ->setCalDavAuth($calDavAuth);

            $this->entityManager->persist($event);

            $addedETags[] = $item['etag'];

            $this->logger->info(\sprintf(
                '[caldav sync]: Entry added: etag %s',
                $event->getSyncHash(),
            ));
        }

        $this->deleteDeprecatedEvents($addedETags, $calDavAuth);

        $this->logService->saveLogEntry($logEntry);

        $this->entityManager->flush();

        $this->logger->info(\sprintf(
            '[caldav sync]: Finished for caldav-auth-id %s',
            $calDavAuth->getId(),
        ));
    }

    /** @param array<string> $eTags */
    public function deleteDeprecatedEvents(array $eTags, CalDavAuth $calDavAuth): void
    {
        $eventsToDelete = $this->eventRepository->findAllByCalDavAuthNotInETagList($calDavAuth, $eTags);

        foreach ($eventsToDelete as $event) {
            $this->entityManager->remove($event);

            $this->logger->info(\sprintf(
                '[caldav sync]: Deprecated event deleted: etag %s',
                $event->getSyncHash(),
            ));
        }

        $this->entityManager->flush();
    }

    public function setSyncedAt(CalDavAuth $auth): void
    {
        $auth->setSyncedAt(new DateTimeImmutable());

        $this->entityManager->persist($auth);
        $this->entityManager->flush();
    }
}
