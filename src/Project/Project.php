<?php

namespace App\Project;

use App\Config;
use App\Exception\DockerSetupException;
use App\FileManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\StyleInterface;
use Symfony\Component\Process\Process;

abstract class Project implements ProjectInterface
{
    /**
     * @var array
     */
    private const PROJECTS = [
        'Symfony Skeleton' => 'symfony/skeleton',
        'Symfony Website Skeleton' => 'symfony/website-skeleton',
        'Laravel Project' => 'laravel/laravel'
    ];

    /**
     * @var int
     */
    protected int $timeOut = 60;

    /**
     * @var string
     */
    protected string $type = '';

    /**
     * @var array
     */
    protected array $processArguments = [
        'composer',
        'create-project'
    ];

    /**
     * @var string
     */
    protected string $projectArgument;

    /**
     * @var FileManager
     */
    protected FileManager $fileManager;

    /**
     * Project constructor.
     * @param string $projectArgument
     */
    public function __construct(string $projectArgument = '')
    {
        $this->projectArgument = $projectArgument;
        $this->fileManager = new FileManager;
    }

    /**
     * @inheritDoc
     */
    public static function newProject(string $projectListKey): ProjectInterface
    {
        preg_match('/([^\/]+)/', self::PROJECTS[$projectListKey], $matches);
        $class = '\\App\\Project\\'.ucfirst($matches[0]).'Project';

        return new $class(self::PROJECTS[$projectListKey]);
    }

    /**
     * @inheritDoc
     */
    abstract public function configure(): void;

    /**
     * @inheritDoc
     */
    public function create(InputInterface $input, StyleInterface $io, Command $command): void
    {
        $this->validate(Config::get('codeDirectory').'/'.$input->getArgument('domain'));
        $this->configure();
        $this->addProcessArgument($this->projectArgument);
        $this->addProcessArgument(Config::get('codeDirectory').'/'.$input->getArgument('domain'));

        $process = new Process($this->processArguments);

        $process->setTimeout($this->timeOut);

        $helper = $command->getApplication()->getHelperSet()->get('process');
        $helper->run($io, $process, null, function ($type, $buffer) use ($io) {
            if (Process::ERR === $type) {
                if ($buffer != '') {
                    $io->formatBuffer($buffer);
                }
            } else {
                $io->greenText("A {$this->type} project has been successfully created...");
            }
        });
    }

    /**
     * @inheritDoc
     */
    public function setTimeout(int $seconds): ProjectInterface
    {
        $this->timeOut = $seconds;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setType(string $type): ProjectInterface
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addProcessArgument(string $argument): ProjectInterface
    {
        $this->processArguments[] = $argument;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public static function getList(): array
    {
        return self::PROJECTS;
    }

    /**
     * Make sure directory is not empty before creating a project.
     *
     * @param $path
     */
    private function validate($path): void
    {
        if ($this->fileManager->exists($path)) {
            throw new DockerSetupException('Project already exists. Aborting...');
        }
    }
}
