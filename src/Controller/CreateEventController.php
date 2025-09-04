<?php

namespace App\Controller;

use App\Entity\Event;
use App\MeetingProvider\AbstractMeetingProvider;
use App\MeetingProvider\MeetingProviderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class CreateEventController extends AbstractController
{
    public function __construct(
        private readonly MeetingProviderService $meetingProviderService,
    )
    {
    }

    public function __invoke(Event $event): Event
    {
        $meetingProvider = $this->meetingProviderService
            ->getProviderByIdentifier($event->getMeetingProviderIdentifier());

        if ($meetingProvider instanceof AbstractMeetingProvider) {
            $participationUrl = $meetingProvider->generateMeetingUrl($event);

            $event->setParticipationUrl($participationUrl);
        }

        return $event;
    }

}
