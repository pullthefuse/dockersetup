<?php

namespace App\Event;

use App\Config;
use App\Docker\DockerComposeInterface;
use App\FileManager;
use App\HostFile;
use App\Server\ServerInterface;
use App\SSL\SSLInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DeleteProjectSubscriber implements EventSubscriberInterface
{
    /**
     * @var FileManager
     */
    protected FileManager $fileManager;

    /**
     * @var SSLInterface
     */
    protected SSLInterface $ssl;

    /**
     * @var DockerComposeInterface
     */
    protected DockerComposeInterface $dockerCompose;

    /**
     * @var ServerInterface
     */
    protected ServerInterface $server;

    /**
     * @var HostFile
     */
    protected HostFile $hostFile;

    /**
     * DeleteProjectSubscriber constructor.
     *
     * @param SSLInterface $ssl
     * @param DockerComposeInterface $dockerCompose
     * @param ServerInterface $server
     * @param HostFile $hostFile
     */
    public function __construct(SSLInterface $ssl, DockerComposeInterface $dockerCompose, ServerInterface $server, HostFile $hostFile)
    {
        $this->fileManager = new FileManager;
        $this->ssl = $ssl;
        $this->dockerCompose = $dockerCompose;
        $this->server = $server;
        $this->hostFile = $hostFile;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            DeleteProjectEvent::NAME => 'onDeleteProject'
        ];
    }

    /**
     * Delete all domain files.
     *
     * @param DeleteProjectEvent $event
     */
    public function onDeleteProject(DeleteProjectEvent $event): void
    {
        if ($event->deleteProjectFiles()) {
            $this->fileManager->addToTrash(Config::get('codeDirectory').'/'.$event->getDomain());
        }

        $this->server->deleteDomain($event->getDomain());
        $this->dockerCompose->delete($event->getDomain());
        $this->ssl->delete($event->getDomain());
        $this->hostFile->removeDomain($event->getDomain());

        $this->fileManager->emptyTrash();
    }
}
