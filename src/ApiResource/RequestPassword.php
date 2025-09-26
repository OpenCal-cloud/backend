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
use App\Controller\RequestPasswordController;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/password/request',
            controller: RequestPasswordController::class,
            openapi: new Model\Operation(
                responses: [
                    '204' => [
                        'description' => 'No content. Password reset request accepted.',
                    ],
                ],
            ),
            read: false,
            write: false,
        ),
    ],
)]
class RequestPassword
{
    private string $email;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
}
