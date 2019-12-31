<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

abstract class AbstractTestCase extends KernelTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
    }

    protected function runApp(array $arguments = [], array $inputArguments = [], $name = 'docker:setup')
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

    public function tearDown(): void
    {
        $filesystem = self::$container->get('filesystem');
        $filesystem->remove('/data/test');

        parent::tearDown();
    }
}
