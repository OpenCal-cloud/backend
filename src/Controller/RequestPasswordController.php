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

namespace App\Controller;

use App\ApiResource\RequestPassword;
use App\Entity\User;
use App\Message\PasswordRequestedMessage;
use App\Repository\UserRepository;
use App\User\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsController]
class RequestPasswordController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserService $userService,
        private readonly EntityManagerInterface $entityManager,
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    public function __invoke(RequestPassword $requestPassword): Response
    {
        $user = $this->userRepository->findOneByEmail($requestPassword->getEmail());

        if (!$user instanceof User) {
            throw $this->createNotFoundException(\sprintf(
                'User with email "%s" not found.',
                $requestPassword->getEmail(),
            ));
        }

        $this->userService->generatePasswordResetToken($user);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->messageBus->dispatch(
            new PasswordRequestedMessage(
                $user->getId(),
            ),
        );

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
