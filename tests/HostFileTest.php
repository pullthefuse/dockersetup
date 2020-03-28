<?php

namespace App\Tests;

class HostFileTest extends AbstractTestCase
{
    /** @test */
    public function add_first_domain_to_current_host_file(): void
    {
        $this->createCurrentHostFile();

        $this->runApp(['domain' => 'dev.example.com'], ['yes', 'no', '0', '0', 0, 'None']);

        $this->assertFileEquals(__DIR__.'/TestFiles/hostsWithExtraData', getenv('DIR').'/etc/hosts');
    }

    /** @test */
    public function add_second_domain_to_current_host_file(): void
    {
        $this->createCurrentHostFile();

        $this->runApp(['domain' => 'dev.example.com'], ['yes', 'no', '0', '0', 0, 'None']);
        $this->runApp(['domain' => 'dev.example2.com'], ['yes', 'no', '0', '0', 0, 'None']);

        $this->assertFileEquals(__DIR__.'/TestFiles/hostsWithTwoDomainsWithExtraData', getenv('DIR').'/etc/hosts');
    }

    /** @test */
    public function create_and_add_to_host_file_if_it_doesnt_exist(): void
    {
        $this->runApp(['domain' => 'dev.example.com'], ['yes', 'no', '0', '0', 0, 'None']);

        $this->assertFileEquals(__DIR__.'/TestFiles/hosts', getenv('DIR').'/etc/hosts');
    }

    /** @test */
    public function check_that_the_correct_command_is_shown_to_add_domain_to_hostfile(): void
    {
        $output = $this->runApp(['domain' => 'dev.example.com'], ['yes', 'no', '0', '0', 0, 'None']);

        $this->assertStringContainsString('sudo bash -c \'echo "127.0.0.1 dev.example.com" >> /etc/hosts\'', $output);
    }

    private function createCurrentHostFile(): void
    {
        $filesystem = self::$container->get('filesystem');
        $content = "127.0.0.1 dev.someOtherDomain.com\n";
        $filesystem->dumpfile(getenv('DIR').'/etc/hosts', $content);
    }
}
