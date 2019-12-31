<?php

namespace App\Tests;

class HostFileTest extends AbstractTestCase
{
    /** @test */
    public function add_first_domain_to_current_host_file()
    {
        $this->createCurrentHostFile();

        $this->runApp(['url' => 'dev.example.com'], ['yes', 'None']);

        $this->assertFileEquals(__DIR__.'/TestFiles/hostsWithExtraData', '/data/test/docker-setup/etc/hosts');
    }

    /** @test */
    public function add_second_domain_to_current_host_file()
    {
        $this->createCurrentHostFile();

        $this->runApp(['url' => 'dev.example.com'], ['yes', 'None']);
        $this->runApp(['url' => 'dev.example2.com'], ['yes', 'None']);

        $this->assertFileEquals(__DIR__.'/TestFiles/hostsWithTwoDomainsWithExtraData', '/data/test/docker-setup/etc/hosts');
    }

    /** @test */
    public function create_and_add_to_host_file_if_it_doesnt_exist()
    {
        $this->runApp(['url' => 'dev.example.com'], ['yes', 'None']);

        $this->assertFileEquals(__DIR__.'/TestFiles/hosts', '/data/test/docker-setup/etc/hosts');
    }

    /** @test */
    public function check_that_the_correct_command_is_shown_to_add_domain_to_hostfile()
    {
        $output = $this->runApp(['url' => 'dev.example.com'], ['yes', 'None']);

        $this->assertStringContainsString('sudo bash -c \'echo "127.0.0.1 dev.example.com" >> /etc/hosts\'', $output);
    }

    private function createCurrentHostFile()
    {
        $filesystem = self::$container->get('filesystem');
        $content = "127.0.0.1 dev.someOtherDomain.com\n";
        $filesystem->dumpfile('/data/test/docker-setup/etc/hosts', $content);
    }
}
