<?php

namespace App\Project;

use App\ConsoleStyle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;

interface ProjectInterface
{
    /**
     * Get the list of projects.
     *
     * @return array
     */
    public static function getList(): array;

    /**
     * Decides which project to return.
     *
     * @param string $projectListKey
     * @return ProjectInterface
     */
    public static function newProject(string $projectListKey): ProjectInterface;

    /**
     * Creates the project.
     *
     * @param InputInterface $input
     * @param ConsoleStyle $io
     * @param Command $command
     * @return mixed
     */
    public function create(InputInterface $input, ConsoleStyle $io, Command $command);

    /**
     * Configure the project.
     *
     * @return mixed
     */
    public function configure(): void;

    /**
     * Add additional arguments.
     *
     * @param string $argument
     * @return mixed
     */
    public function addProcessArgument(string $argument);

    /**
     * Set the process timeout.
     *
     * @param int $seconds
     * @return $this
     */
    public function setTimeout(int $seconds): ProjectInterface;

    /**
     * Set the type that will be displayed.
     *
     * @param string $type
     * @return $this
     */
    public function setType(string $type): ProjectInterface;
}
