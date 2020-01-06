<?php

namespace App\Docker;

use App\Config;
use App\FileManager;
use Symfony\Component\Console\Style\StyleInterface;
use Twig\Environment;

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
     * @var Environment
     */
    private Environment $twig;

    /**
     * DockerCompose constructor.
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
     * Select the containers that need to be loaded.
     *
     * @param StyleInterface $io
     */
    public function interact(StyleInterface $io): void
    {
        $phpVersions = Config::get('docker.services.web.php');
        $this->settings['phpVersion'] = $io->choice('What PHP version do you wish to install?', array_keys($phpVersions), '7.4');

        $databases = Config::get('docker.services.db');
        $this->settings['database'] = $io->choice('What database do you wish to use', array_merge(array_keys($databases), ['None']), 'mysql');

        if ($this->settings['database'] !== 'None') {
            $databaseVersions = Config::get('docker.services.db.'.$this->settings['database']);
            unset($databaseVersions['_default']);
            $this->settings['databaseVersion'] = $io->choice('What database version are you using?', array_keys($databaseVersions), '8.0');
        }
    }

    /**
     * @inheritDoc
     */
    public function create(string $domain, StyleInterface $io, bool $ssl = false): void
    {
        $config = array_merge(Config::all(), [
            'domain' => $domain,
            'ssl' => $ssl,
            'services' => [
                'nginx' => 'docker/nginxBlock.html.twig'
            ]
        ]);

        if ($domain !== 'proxy') {
            $this->interact($io);

            if (isset($this->settings['database'])) {
                $rootDirectory = Config::get('rootDirectory');
                $portMappings = json_decode($this->fileManager->getFileContents("{$rootDirectory}/config/default/databaseMappings.json"),
                    JSON_OBJECT_AS_ARRAY, 512, JSON_THROW_ON_ERROR);
                $db = $config['docker']['services']['db'][$this->settings['database']][$this->settings['databaseVersion']];

                $config['db'] = array_merge([
                    'version' => $this->settings['databaseVersion'] ?? null,
                    'type' => $this->settings['database'],
                    'port' => $portMappings[$this->settings['database']][$this->settings['databaseVersion']],
                    'environment' => $config['docker']['services']['db'][$this->settings['database']]['_default']['environment']
                ], $db);

                $config['services']['db'] = 'docker/databaseBlock.html.twig';
            }

            $config['php'] = [
                'version' => $this->settings['phpVersion']
            ];

            $config['services']['web'] = 'docker/webBlock.html.twig';
        }

        $content = $this->twig->render('docker/base.html.twig', $config);
        $this->fileManager->createFileContent("docker/config/{$domain}.yaml", $content);
    }

    /**
     * @inheritDoc
     */
    public function isSetupComplete(): bool
    {
        return $this->fileManager->exists('docker/config/proxy.yaml');
    }

    /**
     * Setup creates the proxy config.
     *
     * @param StyleInterface $io
     */
    public function setup(StyleInterface $io): void
    {
        $this->create('proxy', $io);
    }

    /**
     * @inheritDoc
     */
    public function delete(string $domain): void
    {
        $this->fileManager->addToTrash(Config::get('rootDirectory').'/docker/config/'.$domain.'.yaml');
    }
}
