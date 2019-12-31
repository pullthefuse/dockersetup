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
}
