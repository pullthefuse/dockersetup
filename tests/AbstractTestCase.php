<?php

namespace App\Tests;

use App\Config;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

abstract class AbstractTestCase extends KernelTestCase
{
    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
    }

    /**
     * Runs the various Symfony commands.
     *
     * @param array $arguments
     * @param array $inputArguments
     * @param string $name
     * @return string
     */
    protected function runApp(array $arguments = [], array $inputArguments = [], $name = 'docker:setup'): string
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);

        $command = $application->find($name);
        $commandTester = new CommandTester($command);
        if (!empty($inputArguments)) {
            $commandTester->setInputs($inputArguments);
        }
        $commandTester->execute(array_merge([
            'command' => $command->getName()
        ], $arguments));

        return $commandTester->getDisplay();
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        $filesystem = self::$container->get('filesystem');
        $filesystem->remove(Config::get('rootDirectory'));

        parent::tearDown();
    }
}
