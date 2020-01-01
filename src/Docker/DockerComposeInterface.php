<?php

namespace App\Docker;

use App\ConsoleStyle;

interface DockerComposeInterface
{
    /**
     * Create the docker-compose file.
     *
     * @param string $domain
     * @param ConsoleStyle $io
     * @param bool $ssl
     */
    public function create(string $domain, ConsoleStyle $io, bool $ssl): void;

    /**
     * Delete Docker-compose file.
     *
     * @param string $domain
     */
    public function delete(string $domain): void;
}
