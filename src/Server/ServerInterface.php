<?php

namespace App\Server;

interface ServerInterface
{
    /**
     * Check whether setup has been run in the past.
     *
     * @return bool
     */
    public function isSetupComplete(): bool;

    /**
     * @return ServerInterface
     */
    public function setup(): ServerInterface;

    /**
     * @return bool
     */
    public function getSSL(): bool;

    /**
     * @param bool $ssl
     * @return ServerInterface
     */
    public function setSSL(bool $ssl): ServerInterface;

    /**
     * Setup the requested domain server.
     *
     * @param string $domain
     * @return mixed
     */
    public function setupDomain(string $domain): void;

    /**
     * Delete the domain files.
     *
     * @param string $domain
     * @return mixed
     */
    public function deleteDomain(string $domain): void;
}
