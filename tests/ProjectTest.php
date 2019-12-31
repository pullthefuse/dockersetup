<?php

namespace App\Tests;

use App\ConsoleStyle;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\NullOutput;

class ProjectTest extends AbstractTestCase
{

    public function create_a_new_symfony_skeleton_project()
    {
        $output = $this->runApp(['url' => 'dev.example.com'], ['yes', '0']);

        $this->assertContains('Creating Symfony Project...', $output);
        $this->assertFileExists('/data/test/docker-setup/code/dev.example.com/composer.json');
    }

    public function create_a_new_symfony_website_project()
    {
        $output = $this->runApp(['url' => 'dev.example.com'], ['yes', '1']);

        $this->assertContains('Creating Symfony Project...', $output);
        $this->assertFileExists('/data/test/docker-setup/code/dev.example.com/composer.json');
    }

    public function create_a_new_laravel_website_project()
    {
        $output = $this->runApp(['url' => 'dev.example.com'], ['yes', '2']);

        $this->assertContains('Creating Laravel Project...', $output);
        $this->assertFileExists('/data/test/docker-setup/code/dev.example.com/composer.json');
    }

    /** @test */
    public function format_buffer_text()
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
    public function check_if_folder_is_empty_before_trying_to_install_a_project()
    {
        $this->runApp(['url' => 'dev.example.com'], ['yes', '0']);

        $this->expectException('App\Exception\DockerSetupException');

        $output = $this->runApp(['url' => 'dev.example.com'], ['yes', '0']);
        $this->assertContains('Project already exists. Aborting...', $output);
    }

    /** @test */
    public function delete_project()
    {
        $this->runApp(['url' => 'dev.example.com'], ['yes', 'None']);

        $this->runApp(['projectName' => 'dev.example.com'], ['yes'], 'docker:project:delete');

        $hostFileContent = file_get_contents('/data/test/docker-setup/etc/hosts');
        $this->assertNotContains('127.0.0.1 dev.example.com', $hostFileContent);
        $this->assertFileNotExists('/data/test/docker-setup/tls/dev.example.com.key');
        $this->assertFileNotExists('/data/test/docker-setup/tls/dev.example.com.crt');
        $this->assertFileNotExists('/data/test/docker-setup/nginx/proxy/sites-enabled/dev.example.com.conf');
        $this->assertFileNotExists('/data/test/docker-setup/nginx/sites-enabled/dev.example.com.conf');
        $this->assertFileNotExists('/data/test/docker-setup/docker/config/dev.example.com.yaml');
        $this->assertDirectoryNotExists('/data/test/docker-setup/code/dev.example.com');
    }

    /** @test */
    public function show_error_when_deleting_project_that_doesnt_exist()
    {
        $this->expectException('App\Exception\DockerSetupException');
        $output = $this->runApp(['projectName' => 'dev.example.com'], ['yes'], 'docker:project:delete');
        $this->assertContains('There are no files to delete...', $output);
    }

    /** @test */
    public function delete_project_but_dont_delete_project_files()
    {
        $this->runApp(['url' => 'dev.example.com'], ['yes', '0']);

        $this->runApp(['projectName' => 'dev.example.com'], ['no'], 'docker:project:delete');

        $hostFileContent = file_get_contents('/data/test/docker-setup/etc/hosts');
        $this->assertNotContains('127.0.0.1 dev.example.com', $hostFileContent);
        $this->assertFileNotExists('/data/test/docker-setup/tls/dev.example.com.key');
        $this->assertFileNotExists('/data/test/docker-setup/tls/dev.example.com.crt');
        $this->assertFileNotExists('/data/test/docker-setup/nginx/proxy/sites-enabled/dev.example.com.conf');
        $this->assertFileNotExists('/data/test/docker-setup/nginx/sites-enabled/dev.example.com.conf');
        $this->assertFileNotExists('/data/test/docker-setup/docker/config/dev.example.com.yaml');
        $this->assertDirectoryExists('/data/test/docker-setup/code/dev.example.com');
    }
}
