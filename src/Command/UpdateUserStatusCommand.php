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

namespace App\Command;

use App\Entity\User;
use App\User\UserService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'opencal:user:status',
    description: 'Enable or disable an existing user account.',
)]
class UpdateUserStatusCommand extends Command
{
    public const string ACTION_ENABLE  = 'enable';
    public const string ACTION_DISABLE = 'disable';

    private const array ALLOWED_ACTIONS = [self::ACTION_ENABLE, self::ACTION_DISABLE];

    public function __construct(
        private readonly UserService $userService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp(<<<'HELP'
This command enables or disables an existing user.

Usage:
  bin/console opencal:user:status enable  user@example.com
  bin/console opencal:user:status disable user@example.com

Arguments:
  action  One of: enable|disable
  email   The user's email address
HELP)
            ->addArgument(
                'action',
                InputArgument::REQUIRED,
                'One of: ' . \implode('|', self::ALLOWED_ACTIONS),
            )
            ->addArgument(
                'email',
                InputArgument::REQUIRED,
                'The email address of the user',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var string $action */
        $action = $input->getArgument('action');

        /** @var string $userEmail */
        $userEmail = $input->getArgument('email');

        if (!$this->isValidAction($action)) {
            $io->error(\sprintf(
                'Invalid action "%s". Allowed values are: %s.',
                $action,
                \implode(', ', self::ALLOWED_ACTIONS),
            ));

            return Command::FAILURE;
        }

        if (!$this->isValidEmail($userEmail)) {
            $io->error('Please enter a valid email address.');

            return Command::FAILURE;
        }

        $user = $this->userService->findOneByEmail($userEmail);

        if (!$user instanceof User) {
            $io->error(\sprintf('No user found with email address %s.', $userEmail));

            return Command::FAILURE;
        }

        $this->applyAction($action, $user);
        $this->userService->saveUser($user);

        $io->success($this->successMessage($action, $user->getEmail()));

        return Command::SUCCESS;
    }

    private function isValidAction(string $action): bool
    {
        return \in_array(\strtolower($action), self::ALLOWED_ACTIONS, true);
    }

    private function isValidEmail(string $email): bool
    {
        return false !== \filter_var($email, \FILTER_VALIDATE_EMAIL);
    }

    private function applyAction(string $action, User $user): void
    {
        $normalized = \strtolower($action);

        if (self::ACTION_ENABLE === $normalized) {
            $this->userService->enableUser($user);

            return;
        }

        $this->userService->disableUser($user);
    }

    private function successMessage(string $action, string $email): string
    {
        return self::ACTION_ENABLE === $action
            ? \sprintf('The user account with email "%s" has been enabled.', $email)
            : \sprintf('The user account with email "%s" has been disabled.', $email);
    }
}
