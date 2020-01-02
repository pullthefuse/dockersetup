<?php

namespace App\Tests;

class DockerComposeFileTest extends AbstractTestCase
{
    /** @test */
    public function show_the_correct_docker_compose_command_on_success()
    {
        $output = $this->runApp([], ['dev.example.com', 'yes', '0', '0', 0, 'None']);

        $this->assertStringContainsString('docker-compose -f /data/test/docker-setup/docker/config/dev.example.com.yaml up -d', $output);
    }

    /** @test */
    public function create_docker_compose_file_without_domain_argument_and_domain_question_answered()
    {
        $output = $this->runApp([], ['dev.example.com', 'yes', '0', '0', 0, 'None']);

        $this->assertContains('What is your domain?', $output);
        $this->assertContains('Creating docker-compose file...', $output);
        $this->assertFileEquals(__DIR__.'/TestFiles/dockerCompose.yaml', '/data/test/docker-setup/docker/config/dev.example.com.yaml');
    }

    /** @test */
    public function create_docker_compose_file_without_domain_argument_and_domain_question_empty()
    {
        $this->expectException('App\Exception\DockerSetupException');

        $output = $this->runApp([], ['', 'yes', '0', '0', 0, 'None']);

        $this->assertContains('Exiting setup...', $output);
        $this->assertFileNotExists('/data/test/docker-setup/docker/config/dev.example.com.yaml');
    }

    /** @test */
    public function create_docker_compose_file_with_domain_argument()
    {
        $output = $this->runApp(['url' => 'dev.example.com'], ['yes', '0', '0', 0, 'None']);

        $this->assertNotContains('What is your domain?', $output);
        $this->assertContains('Creating docker-compose file...', $output);
        $this->assertFileEquals(__DIR__.'/TestFiles/dockerCompose.yaml', '/data/test/docker-setup/docker/config/dev.example.com.yaml');
    }

    /** @test */
    public function create_docker_compose_file_without_ssl()
    {
        $output = $this->runApp(['url' => 'dev.example.com'], ['no', '0', '0', 0, 'None']);

        $this->assertContains('Creating docker-compose file...', $output);
        $this->assertFileEquals(__DIR__.'/TestFiles/dockerComposeWithoutSSL.yaml', '/data/test/docker-setup/docker/config/dev.example.com.yaml');
    }

    /** @test */
    public function only_allow_setup_to_be_run_once_with_a_domain()
    {
        $this->runApp(['url' => 'dev.example.com'], ['yes', '0', '0', 0, 'None']);

        $this->expectException('App\Exception\DockerSetupException');

        $output = $this->runApp(['url' => 'dev.example.com'], ['yes']);

        $this->assertContains('Domain already exists', $output);
    }
}
