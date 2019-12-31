<?php

namespace App\Docker;

interface DockerComposeInterface
{
    /**
     * Create the docker-compose file.
     *
     * @param string $domain
     * @param bool $ssl
     */
    public function create(string $domain, bool $ssl): void;

    /**
     * Delete Docker-compose file.
     *
     * @param string $domain
     */
    public function delete(string $domain): void;
}
