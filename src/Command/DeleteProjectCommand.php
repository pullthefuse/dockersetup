<?php

namespace App\Command;

use App\ConsoleStyle;
use App\Event\DeleteProjectEvent;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DeleteProjectCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'docker:project:delete';

    /**
     * @var EventDispatcherInterface
     */
    protected EventDispatcherInterface $dispatcher;

    /**
     * @var ConsoleStyle
     */
    protected ConsoleStyle $io;

    /**
     * DeleteProjectCommand constructor.
     *
     * @param EventDispatcherInterface $dispatcher
     * @param string|null $name
     */
    public function __construct(EventDispatcherInterface $dispatcher, string $name = null)
    {
        $this->dispatcher = $dispatcher;

        parent::__construct($name);
    }

    /**
     * @inheritDoc
     */
    public function configure(): void
    {
        $this
            ->setDescription('creates a project')
            ->addArgument('projectName', InputArgument::REQUIRED)
            ->setHelp('Delete a project');
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
        $deleteProjectFiles = $this->io->confirm('Do you want to delete project files?');

        $this->dispatcher->dispatch(new DeleteProjectEvent($input->getArgument('projectName'), $deleteProjectFiles), DeleteProjectEvent::NAME);

        return 0;
    }
}
