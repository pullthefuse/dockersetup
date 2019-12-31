<?php

namespace App\Docker;

use App\Config;
use App\FileManager;

class DockerCompose implements DockerComposeInterface
{
    /**
     * @var FileManager
     */
    private FileManager $fileManager;

    /**
     * DockerCompose constructor.
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
    public function create(string $domain, bool $ssl): void
    {
        $config = array_merge(Config::all(), [
            'domain' => $domain,
            'ssl' => $ssl,
            'database' => [
                'version' => '8.0',
                'type' => 'mysql'
            ]
        ]);

        $content = $this->fileManager->parseTemplate(__DIR__.'/../Templates/DockerCompose/dockerComposeTemplate.php', $config);

        $this->fileManager->createFileContent("docker/config/{$domain}.yaml", $content);
    }

    /**
     * @inheritDoc
     */
    public function delete(string $domain): void
    {
        $this->fileManager->addToTrash(Config::get('rootDirectory').'/docker/config/'.$domain.'.yaml');
    }
}
