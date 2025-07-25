<?php

declare(strict_types=1);

namespace App\CalDav;

use Sabre\DAV\Client as SabreDAVClient;

class ClientFactory
{
    public function __construct()
    {
    }

    public function getClient(
        string $baseUri,
        string $username,
        string $password,
    ): SabreDAVClient {
        $settings = [
            'baseUri'  => $baseUri,
            'userName' => $username,
            'password' => $password,
        ];

        return new SabreDAVClient($settings);
    }
}
