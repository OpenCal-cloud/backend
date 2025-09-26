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

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model;
use App\Controller\CancelEventController;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/events/{id}/cancel',
            controller: CancelEventController::class,
            openapi: new Model\Operation(
                description: 'Cancels a specific event. Requires a valid cancellationHash ' .
                'to authorize the cancellation.',
                requestBody: new Model\RequestBody(
                    description: 'The cancellation hash',
                ),
            ),
        ),
    ],
)]
class CancelEvent
{
    private string $cancellationHash;

    public function getCancellationHash(): string
    {
        return $this->cancellationHash;
    }

    public function setCancellationHash(string $cancellationHash): void
    {
        $this->cancellationHash = $cancellationHash;
    }
}
