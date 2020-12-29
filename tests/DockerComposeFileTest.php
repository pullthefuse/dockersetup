<?php

namespace App\Tests;

use App\Exception\DockerSetupException;

class DockerComposeFileTest extends AbstractTestCase
{
    /** @test */
    public function show_the_correct_docker_compose_command_on_success(): void
    {
        $output = $this->runApp([], ['dev.example.com', 'yes', 'no', '0', '0', 0, 'None']);

        $this->assertStringContainsString('docker-compose -f '.getenv('DIR').'/docker/config/dev.example.com.yaml up -d', $output);
    }

    /** @test */
    public function create_docker_compose_file_without_domain_argument_and_domain_question_answered(): void
    {
        $output = $this->runApp([], ['dev.example.com', 'yes', 'no', '0', '0', 0, 'None']);

        $this->assertStringContainsString('What is your domain?', $output);
        $this->assertStringContainsString('Creating docker-compose file...', $output);
        $this->assertFileEquals(__DIR__.'/TestFiles/dockerCompose.yaml', getenv('DIR').'/docker/config/dev.example.com.yaml');
    }

    /** @test */
    public function create_docker_compose_file_without_domain_argument_and_domain_question_empty(): void
    {
        $this->expectException(DockerSetupException::class);

        $output = $this->runApp([], ['', 'yes', 'no', '0', '0', 0, 'None']);

        $this->assertStringContainsString('Exiting setup...', $output);
        $this->assertFileNotExists(getenv('DIR').'/docker/config/dev.example.com.yaml');
    }

    /** @test */
    public function create_docker_compose_file_with_domain_argument(): void
    {
        $output = $this->runApp(['domain' => 'dev.example.com'], ['yes', 'no', '0', '0', 0, 'None']);

        $this->assertStringNotContainsString('What is your domain?', $output);
        $this->assertStringContainsString('Creating docker-compose file...', $output);
        $this->assertFileEquals(__DIR__.'/TestFiles/dockerCompose.yaml', getenv('DIR').'/docker/config/dev.example.com.yaml');
    }

    /** @test */
    public function create_docker_compose_file_without_ssl(): void
    {
        $output = $this->runApp(['domain' => 'dev.example.com'], ['no', 'no', '0', '0', 0, 'None']);

        $this->assertStringContainsString('Creating docker-compose file...', $output);
        $this->assertFileEquals(__DIR__.'/TestFiles/dockerComposeWithoutSSL.yaml', getenv('DIR').'/docker/config/dev.example.com.yaml');
    }

    /** @test */
    public function create_docker_compose_file_with_nfs(): void
    {
        $output = $this->runApp(['domain' => 'dev.example.com'], ['yes', 'yes', '0', '0', 0, 'None']);

        $this->assertStringContainsString('Creating docker-compose file...', $output);
        $this->assertFileEquals(__DIR__.'/TestFiles/dockerComposeWithNFS.yaml', getenv('DIR').'/docker/config/dev.example.com.yaml');
    }

    /** @test */
    public function only_allow_setup_to_be_run_once_with_a_domain(): void
    {
        $this->runApp(['domain' => 'dev.example.com'], ['yes', 'no', '0', '0', 0, 'None']);

        $this->expectException(DockerSetupException::class);

        $output = $this->runApp(['domain' => 'dev.example.com'], ['yes']);

        $this->assertStringContainsString('Domain already exists', $output);
    }

    /** @test */
    public function check_that_exception_is_thrown_if_ds_proxy_or_proxy_is_used_as_domain_name(): void
    {
        $this->expectException(DockerSetupException::class);

        $output = $this->runApp(['domain' => 'proxy', ['yes', 'no']]);

        $this->assertStringContainsString('Proxy cannot be set as a domain. Aborting...', $output);
    }
}
