<?php

namespace App\Tests;

use App\ConsoleStyle;
use App\Exception\DockerSetupException;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\NullOutput;

class ProjectTest extends AbstractTestCase
{

    public function create_a_new_symfony_skeleton_project(): void
    {
        $output = $this->runApp(['domain' => 'dev.example.com'], ['yes', 'no', '0', '0', 0, '0']);

        $this->assertStringContainsString('A Symfony project has been successfully created...', $output);
        $this->assertFileExists(getenv('DIR').'/code/dev.example.com/composer.json');
    }

    public function create_a_new_symfony_website_project(): void
    {
        $output = $this->runApp(['domain' => 'dev.example.com'], ['yes', 'no', '0', '0', 0, '1']);

        $this->assertStringContainsString('A Symfony project has been successfully created...', $output);
        $this->assertFileExists(getenv('DIR').'/code/dev.example.com/composer.json');
    }

    public function create_a_new_laravel_website_project(): void
    {
        $output = $this->runApp(['domain' => 'dev.example.com'], ['yes', 'no', '0', '0', 0, '2']);

        $this->assertStringContainsString('A Laravel project has been successfully created...', $output);
        $this->assertFileExists(getenv('DIR').'/code/dev.example.com/composer.json');
    }

    /** @test */
    public function format_buffer_text(): void
    {
        $io = new ConsoleStyle(new ArgvInput(), new NullOutput());
        $buffer = [
            '- Installing symfony/yaml (v5.0.2): Loading from cache' => '- Installing<fg=green;bg=default> symfony/yaml </>(<fg=yellow;bg=default>v5.0.2</>): Loading from cache',
            'Symfony operations: 1 recipe (b17efb388597b3ff9f5c1ff111041a10) - Configuring symfony/flex (>=1.0): From github.com/symfony/recipes:master' => 'Symfony operations: 1 recipe (b17efb388597b3ff9f5c1ff111041a10) - Configuring<fg=green;bg=default> symfony/flex </>(<fg=yellow;bg=default>>=1.0</>): From github.com/symfony/recipes:master'
        ];

        foreach ($buffer as $key => $bufferLine) {
            $test = $io->setColors($key);
            $this->assertEquals($bufferLine, $test);
        }
    }

    /** @test */
    public function check_if_folder_is_empty_before_trying_to_install_a_project(): void
    {
        $this->runApp(['domain' => 'dev.example.com'], ['yes', 'no', '0', '0', 0, '0']);

        $this->expectException(DockerSetupException::class);

        $output = $this->runApp(['domain' => 'dev.example.com'], ['yes', 'no', '0', '0', 0, '0']);
        $this->assertStringContainsString('Project already exists. Aborting...', $output);
    }

    /** @test */
    public function delete_project(): void
    {
        $this->runApp(['domain' => 'dev.example.com'], ['yes', 'no', '0', '0', 0, 'None']);

        $this->runApp(['domain' => 'dev.example.com'], ['yes'], 'docker:project:delete');

        $hostFileContent = file_get_contents(getenv('DIR').'/etc/hosts');
        $this->assertStringNotContainsString('127.0.0.1 dev.example.com', $hostFileContent);
        $this->assertFileNotExists(getenv('DIR').'/tls/dev.example.com.key');
        $this->assertFileNotExists(getenv('DIR').'/tls/dev.example.com.crt');
        $this->assertFileNotExists(getenv('DIR').'/nginx/proxy/sites-enabled/dev.example.com.conf');
        $this->assertFileNotExists(getenv('DIR').'/nginx/sites-enabled/dev.example.com.conf');
        $this->assertFileNotExists(getenv('DIR').'/docker/config/dev.example.com.yaml');
        $this->assertDirectoryNotExists(getenv('DIR').'/code/dev.example.com');
    }

    /** @test */
    public function show_error_when_deleting_project_that_doesnt_exist(): void
    {
        $this->expectException(DockerSetupException::class);
        $output = $this->runApp(['domain' => 'dev.example.com'], ['yes'], 'docker:project:delete');
        $this->assertStringContainsString('There are no files to delete...', $output);
    }

    /** @test */
    public function delete_project_but_dont_delete_project_files(): void
    {
        $this->runApp(['domain' => 'dev.example.com'], ['yes', 'no', '0', '0', 0, '0']);

        $this->runApp(['domain' => 'dev.example.com'], ['no'], 'docker:project:delete');

        $hostFileContent = file_get_contents(getenv('DIR').'/etc/hosts');
        $this->assertStringNotContainsString('127.0.0.1 dev.example.com', $hostFileContent);
        $this->assertFileNotExists(getenv('DIR').'/tls/dev.example.com.key');
        $this->assertFileNotExists(getenv('DIR').'/tls/dev.example.com.crt');
        $this->assertFileNotExists(getenv('DIR').'/nginx/proxy/sites-enabled/dev.example.com.conf');
        $this->assertFileNotExists(getenv('DIR').'/nginx/sites-enabled/dev.example.com.conf');
        $this->assertFileNotExists(getenv('DIR').'/docker/config/dev.example.com.yaml');
        $this->assertDirectoryExists(getenv('DIR').'/code/dev.example.com');
    }
}
