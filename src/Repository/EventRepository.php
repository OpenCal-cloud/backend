<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CalDavAuth;
use App\Entity\Event;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Safe\DateTime;

/** @extends ServiceEntityRepository<Event> */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    /** @return array<Event> */
    public function findAllByDayByUser(User $user, \DateTimeInterface $day): array
    {
        /** @var array<Event> $result */
        $result = $this->createQueryBuilder('event')
            ->leftJoin('event.eventType', 'event_type')
            ->leftJoin('event_type.host', 'user')
            ->where('user.id = :user_id')
            ->andWhere('event.day = :today')
            ->setParameter('user_id', $user->getId())
            ->setParameter('today', $day)
            ->getQuery()
            ->getResult();

        return $result;
    }

    public function findOneBySyncHashAndUser(string $syncHash, User $user): ?Event
    {
        /** @var ?Event $event */
        $event = $this->createQueryBuilder('event')
            ->leftJoin('event.eventType', 'event_type')
            ->leftJoin('event_type.host', 'user')
            ->where('user.id = :user_id')
            ->andWhere('event.syncHash = :sync_hash')
            ->setParameter('sync_hash', $syncHash)
            ->setParameter('user_id', $user->getId())
            ->getQuery()
            ->getOneOrNullResult();

        return $event;
    }

    /**
     * @param array<string> $eTagList
     *
     * @return array<Event>
     */
    public function findAllByCalDavAuthNotInETagList(CalDavAuth $auth, array $eTagList): array
    {
        /** @var array<Event> $result */
        $result = $this->createQueryBuilder('event')
            ->leftJoin('event.calDavAuth', 'auth')
            ->where('auth.id = :auth_id')
            ->andWhere('event.syncHash NOT IN (:e_tag_list)')
            ->setParameter('auth_id', $auth->getId())
            ->setParameter('e_tag_list', $eTagList)
            ->getQuery()
            ->getResult();

        return $result;
    }

    /** @return array<Event> */
    public function findUpcomingEvents(): array
    {
        /** @var array<Event> $result */
        $result = $this->createQueryBuilder('event')
            ->where('event.syncHash IS NULL')
            ->andWhere('event.day >= DATE(:date_yesterday) AND event.day <= DATE(:date_tomorrow)')
            ->andWhere('event.startTime >= TIME(:time_now) AND event.startTime <= TIME(:time_p30m)')
            ->andWhere('event.syncHash IS NULL')
            ->setParameter('date_yesterday', new DateTime('yesterday')->format('Y-m-d'))
            ->setParameter('date_tomorrow', new DateTime('tomorrow')->format('Y-m-d'))
            ->setParameter('time_now', new DateTime('now')->format('H:i:s'))
            ->setParameter('time_p30m', new DateTime('now +30 min')->format('H:i:s'))
            ->getQuery()
            ->getResult();

        return $result;
    }
}
