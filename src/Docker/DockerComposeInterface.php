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
    public function create(string $domain, ConsoleStyle $io, bool $ssl = false): void;

    /**
     * Delete Docker-compose file.
     *
     * @param string $domain
     */
    public function delete(string $domain): void;

    /**
     * Check whether setup has been run in the past.
     *
     * @return bool
     */
    public function isSetupComplete(): bool;

    /**
     * @param ConsoleStyle $io
     */
    public function setup(ConsoleStyle $io): void;
}
