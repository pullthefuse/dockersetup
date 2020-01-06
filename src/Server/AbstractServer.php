<?php

namespace App\Server;

use App\FileManager;
use Twig\Environment;

abstract class AbstractServer implements ServerInterface
{
    /**
     * @var FileManager
     */
    protected FileManager $fileManager;

    /**
     * @var Environment
     */
    protected Environment $twig;

    /**
     * @var bool
     */
    private bool $ssl = false;

    /**
     * AbstractServer constructor.
     *
     * @param FileManager $fileManager
     * @param Environment $twig
     */
    public function __construct(FileManager $fileManager, Environment $twig)
    {
        $this->fileManager = $fileManager;
        $this->twig = $twig;
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
