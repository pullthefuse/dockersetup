<?php

namespace App\Command;

use App\ConsoleStyle;
use App\Project\Project;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\StyleInterface;

class CreateProjectCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'docker:project:create';

    /**
     * @var StyleInterface
     */
    private StyleInterface $io;

    /**
     * @inheritDoc
     */
    public function configure(): void
    {
        $this
            ->setDescription('creates a project')
            ->addArgument('domain', InputArgument::REQUIRED)
            ->setHelp('Create a new project');
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
        $question = new ChoiceQuestion('Create a new project?', array_keys(Project::getList()));
        $project = Project::newProject($this->io->askQuestion($question));

        $project->create($input, $output, $this->io, $this);

        return 0;
    }
}
