<?php

declare(strict_types=1);

namespace App\Notification\Email;

use App\Entity\Event;
use App\Entity\EventType;
use App\Mail\MailService;
use Symfony\Contracts\Translation\TranslatorInterface;

class ReminderToHostEmailNotification extends AbstractEmailNotificationService
{
    public function __construct(
        MailService $mailService,
        string $frontendDomain,
        bool $useSSL,
        private readonly TranslatorInterface $translator,
        private readonly string $locale,
    ) {
        parent::__construct(
            $mailService,
            $frontendDomain,
            $useSSL,
        );
    }

    public function sendNotification(Event $event): void
    {
        if (!$event->getEventType() instanceof EventType) {
            throw new \RuntimeException('Event has no event type');
        }

        $params = $this->getParams($event);

        $this->sendEmail(
            $this->translator->trans('mails.event_reminder.to_host.subject', $params, 'messages', $this->locale),
            $this->translator->trans('mails.event_reminder.to_host.message', $params, 'messages', $this->locale),
            $event->getEventType()->getHost()->getEmail(),
            \sprintf(
                '%s %s',
                $event->getEventType()->getHost()->getGivenName(),
                $event->getEventType()->getHost()->getFamilyName(),
            ),
        );
    }

    /** @return array<string, string|int|null> */
    private function getParams(Event $event): array
    {
        if (!$event->getEventType() instanceof EventType) {
            throw new \RuntimeException('Event has no event type');
        }

        return [
            '{attendee_name}'   => $event->getParticipantName() ?? '',
            '{time_from}'       => $event->getStartTime()->format('H:i'),
            '{booking_date}'    => $event->getDay()->format('d.m.Y'),
            '{event_type_name}' => $event->getEventType()->getName(),
            '{duration}'        => $event->getEventType()->getDuration(),
            '{email_attendee}'  => $event->getParticipantEmail() ?? '',
            '{given_name}'      => $event->getEventType()->getHost()->getGivenName(),
            '{family_name}'     => $event->getEventType()->getHost()->getFamilyName(),
            '{frontend_url}'    => $this->getFrontendUrl(),
            '{location_name}'   => $event->getParticipationUrl(),
        ];
    }
}
