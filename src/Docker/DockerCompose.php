<?php

namespace App\Docker;

use App\Config;
use App\ConsoleStyle;
use App\FileManager;

class DockerCompose implements DockerComposeInterface
{
    /**
     * @var FileManager
     */
    private FileManager $fileManager;

    /**
     * @var array
     */
    private array $settings = [];

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
     * Select the containers that need to be loaded.
     *
     * @param ConsoleStyle $io
     */
    public function interact(ConsoleStyle $io)
    {
        $phpVersions = Config::get('docker.services.web.php');
        $this->settings['phpVersion'] = $io->choice('What PHP version do you wish to install?', array_keys($phpVersions), '7.4');

        $databases = Config::get('docker.services.db');
        $this->settings['database'] = $io->choice('What database do you wish to use', array_merge(array_keys($databases), ['None']));

        if ($this->settings['database'] !== 'None') {
            $databaseVersions = Config::get('docker.services.db.'.$this->settings['database']);
            $this->settings['databaseVersion'] = $io->choice('What database version are you using?', array_keys($databaseVersions));
        }
    }

    /**
     * @inheritDoc
     */
    public function create(string $domain, ConsoleStyle $io, bool $ssl): void
    {
        $this->interact($io);

        $config = array_merge(Config::all(), [
            'domain' => $domain,
            'ssl' => $ssl,
            'database' => [
                'version' => $this->settings['databaseVersion'] ?? null,
                'type' => $this->settings['database']
            ],
            'php' => [
                'version' => $this->settings['phpVersion']
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
