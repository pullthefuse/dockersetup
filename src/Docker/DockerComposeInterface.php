<?php

namespace App\Docker;

use Symfony\Component\Console\Style\StyleInterface;

interface DockerComposeInterface
{
    /**
     * Create the docker-compose file.
     *
     * @param string $domain
     * @param StyleInterface $io
     * @param bool $ssl
     */
    public function create(string $domain, StyleInterface $io, bool $ssl = false): void;

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
     * Setup the proxy file.
     *
     * @param StyleInterface $io
     */
    public function setup(StyleInterface $io): void;
}
