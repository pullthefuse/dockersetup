<?php

namespace App\Server;

use App\FileManager;

abstract class AbstractServer implements ServerInterface
{
    /**
     * @var FileManager
     */
    protected FileManager $fileManager;

    /**
     * @var bool
     */
    private bool $ssl = false;

    /**
     * AbstractServer constructor.
     *
     * @param FileManager $fileManager
     */
    public function __construct(FileManager $fileManager)
    {
        $this->fileManager = $fileManager;
    }

    /**
     * @inheritDoc
     */
    abstract public function isSetupComplete(): bool;

    /**
     * @inheritDoc
     */
    abstract public function deleteDomain(string $domain): void;

    /**
     * @inheritDoc
     */
    public function setSSL(bool $ssl): ServerInterface
    {
        $this->ssl = $ssl;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSSL(): bool
    {
        return $this->ssl;
    }
}
