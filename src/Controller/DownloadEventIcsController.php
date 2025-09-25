<?php

declare(strict_types=1);

namespace App\Controller;

use App\CalDav\ExportEventService;
use App\Entity\Event;
use App\Entity\User;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[Route('/events/{id}/ics')]
class DownloadEventIcsController extends AbstractController
{
    public function __construct(
        private readonly ExportEventService $exportEventService,
        private readonly EventRepository $eventRepository,
    ) {
    }

    public function __invoke(int $id): BinaryFileResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new AccessDeniedHttpException();
        }

        $event = $this->eventRepository->find($id);

        if (!$event instanceof Event) {
            throw new NotFoundHttpException();
        }

        if ($event->getEventType()?->getHost()->getId() !== $user->getId()) {
            throw new AccessDeniedHttpException();
        }

        $icsFilePath = $this->exportEventService->exportEvent($event);

        return new BinaryFileResponse($icsFilePath);
    }
}
