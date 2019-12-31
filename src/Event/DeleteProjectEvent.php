<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class DeleteProjectEvent extends Event
{
    /**
     * @var string
     */
    public const NAME = 'project.delete';

    /**
     * @var string
     */
    protected string $domain;

    /**
     * @var bool
     */
    protected bool $deleteProjectFiles;

    /**
     * DeleteProjectEvent constructor.
     *
     * @param string $domain
     * @param bool $deleteProjectFiles
     */
    public function __construct(string $domain, bool $deleteProjectFiles)
    {
        $this->domain = $domain;
        $this->deleteProjectFiles = $deleteProjectFiles;
    }

    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * Should project files be deleted.
     */
    public function deleteProjectFiles(): bool
    {
        return $this->deleteProjectFiles;
    }
}
