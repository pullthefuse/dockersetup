<?php

namespace App\Command;

use App\Config;
use App\ConsoleStyle;
use App\Exception\DockerSetupException;
use App\HostFile;
use App\Docker\DockerComposeInterface;
use App\FileManager;
use App\Project\Project;
use App\Server\ServerInterface;
use App\SSL\SSLInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class SetupCommand extends Command
{
    /**
     * @var FileManager
     */
    private FileManager $fileManager;

    /**
     * @var DockerComposeInterface
     */
    private DockerComposeInterface $dockerCompose;

    /**
     * @var ServerInterface
     */
    private ServerInterface $server;

    /**
     * @var HostFile
     */
    private HostFile $hostFile;

    /**
     * @var ConsoleStyle
     */
    private ConsoleStyle $io;

    /**
     * @var SSLInterface
     */
    private SSLInterface $ssl;

    /**
     * SetupCommand constructor.
     *
     * @param SSLInterface $ssl
     * @param HostFile $hostFile
     * @param DockerComposeInterface $dockerCompose
     * @param ServerInterface $server
     * @param FileManager $fileManager
     * @param string|null $name
     */
    public function __construct(SSLInterface $ssl, HostFile $hostFile, DockerComposeInterface $dockerCompose, ServerInterface $server, FileManager $fileManager, string $name = null)
    {
        $this->dockerCompose = $dockerCompose;
        $this->fileManager = $fileManager;
        $this->server = $server;
        $this->ssl = $ssl;
        $this->hostFile = $hostFile;

        parent::__construct($name);
    }

    /**
     * @inheritDoc
     */
    public function configure(): void
    {
        $this
            ->setName('docker:setup')
            ->setDescription('creates your docker environment')
            ->addArgument('url', InputArgument::OPTIONAL)
            ->addArgument('projectName', InputArgument::OPTIONAL)
            ->setHelp('More to come!');
    }

    /**
     * @inheritDoc
     */
    public function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new ConsoleStyle($input, $output);
    }

    /**
     * @inheritDoc
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io->clearScreen();

        if (!$this->server->isSetupComplete()) {
            $this->server->setup();
        }

        $domain = $this->getDomain($input);

        $this->hostFile->addToHostFile($domain);

        $ssl = $this->ssl->setup($this->io, $domain);

        $this->io->text('Creating server files...');

        $this->server->setupDomain($domain);

        $this->io->text('Creating docker-compose file...');

        $this->dockerCompose->create($domain, $ssl);

        $input->setArgument('projectName', $domain);
        $question = new ChoiceQuestion('Create a new project?', array_merge(array_keys(Project::getList()), ['None']));
        $answer = $this->io->askQuestion($question);

        if ($answer !== 'None') {
            $project = Project::newProject($answer);
            $project->create($input, $this->io, $this);
        }

        $rootDirectory = Config::get('rootDirectory');
        $ipAddress = Config::get('host_ip_address');

        $this->io->success(
            <<<EOT
            $domain was successfully setup.
            
            You will need to add your domain to your hosts file, either set it manually or run the following command:
            
                sudo bash -c 'echo "{$ipAddress} {$domain}" >> /etc/hosts'
            
            You will need to start the proxy container first, this allows you to have multiple nginx containers up at the same time.
            
                docker-compose -f {$rootDirectory}/docker/config/proxy.yaml up -d
                
            To start your {$domain} docker containers run the following:
            
                docker-compose -f {$rootDirectory}/docker/config/{$domain}.yaml up -d
            EOT
        );

        return 0;
    }

    /**
     * Get the domain from the input argument or ask for it.
     *
     * @param InputInterface $input
     * @return string
     */
    private function getDomain(InputInterface $input): string
    {
        if (null === $domain = $input->getArgument('url')) {
            $question = new Question('What is your domain?');

            if (null === $domain = $this->io->askQuestion($question)) {
                throw new DockerSetupException('Exiting setup...');
            }
        }

        $rootDirectory = Config::get('rootDirectory');

        if ($this->fileManager->exists("{$rootDirectory}/docker/config/{$domain}.yaml")) {
            throw new DockerSetupException('Domain already exists. Aborting...');
        }

        return $domain;
    }
}
