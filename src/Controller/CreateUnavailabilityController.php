<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Unavailability;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

#[AsController]
class CreateUnavailabilityController extends AbstractController
{
    public function __invoke(Unavailability $unavailability): Unavailability
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new AccessDeniedHttpException();
        }

        $unavailability->setUser($user);

        return $unavailability;
    }
}
