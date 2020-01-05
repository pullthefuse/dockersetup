<?php

namespace App\Tests;

use App\Config;

class ConfigTest extends AbstractTestCase
{
    /** @test */
    public function get_a_parameter_from_the_config_class_using_camel_case()
    {
        $rootDirectory = Config::get('rootDirectory');

        $this->assertEquals('/data/test/docker-setup', $rootDirectory);
    }

    /** @test */
    public function get_a_parameter_from_the_config_class_using_snake_case()
    {
        $rootDirectory = Config::get('root_directory');

        $this->assertEquals('/data/test/docker-setup', $rootDirectory);
    }

    /** @test */
    public function get_empty_string_if_parameter_doesnt_exist()
    {
        $value = Config::get('testValue');

        $this->assertEquals('', $value);
    }

    /** @test */
    public function generate_port_mappings_on_setup()
    {
        $this->runApp(['domain' => 'dev.example.com'], ['yes', '0', '0', '0', 'None']);

        $this->assertEquals($this->portMappings(), file_get_contents('/data/test/docker-setup/config/default/databaseMappings.json'));
    }

    private function portMappings()
    {
        return json_encode([
            'mysql' => [
                '8.0' => '3306:3306',
                '5.7' => '3305:3306',
                '5.6' => '3304:3306'
            ],
            'postgres' => [
                '12' => '5432:5432',
                '11' => '5431:5432',
                '10' => '5430:5432'
            ]
        ]);
    }
}
