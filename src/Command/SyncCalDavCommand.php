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

use App\Message\SyncCalDavMessage;
use Safe\DateTimeImmutable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'opencal:sync:caldav',
    description: 'Syncs the caldav calendars with the database',
)]
class SyncCalDavCommand extends Command
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
    ) {
        parent::__construct();
    }

    /** @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $start = new DateTimeImmutable();
        $output->writeln('Trigger calendar sync at ' . $start->format('Y-m-d H:i:s'));

        $this->messageBus->dispatch(new SyncCalDavMessage());

        return self::SUCCESS;
    }
}
