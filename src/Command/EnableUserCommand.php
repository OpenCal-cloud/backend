<?php

declare(strict_types=1);

namespace App\Command;

use App\User\UserService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\User\UserInterface;

#[AsCommand(
    name: 'opencal:user:enable',
    description: 'Enable an existing user',
)]
class EnableUserCommand extends Command
{
    public function __construct(
        private readonly UserService $userService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp('This command allows you to enable an existing user.')
            ->addArgument(
                'email',
                InputArgument::REQUIRED,
                'A email address of the user',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var string $userEmail */
        $userEmail = $input->getArgument('email');

        if (false === \filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
            $io->error('Please enter a valid email address.');

            return Command::FAILURE;
        }

        $user = $this->userService->findOneByEmail($userEmail);

        if (!$user instanceof UserInterface) {
            $io->error(\sprintf(
                'No user found with email address %s.',
                $userEmail,
            ));

            return Command::FAILURE;
        }

        $this->userService->enableUser($user);
        $this->userService->saveUser($user);

        $io->success(\sprintf(
            'The user account with email "%s" has been enabled.',
            $user->getEmail(),
        ));

        return Command::SUCCESS;
    }
}
