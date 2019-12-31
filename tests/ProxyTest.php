<?php

namespace App\Tests;

class ProxyTest extends AbstractTestCase
{
    /** @test */
    public function check_that_proxy_docker_composer_is_created_during_setup()
    {
        self::$container->get('App\Server\NginxServer')->setup();

        $this->assertFileEquals(__DIR__.'/TestFiles/dockerComposeProxy.yaml', '/data/test/docker-setup/docker/config/proxy.yaml');
    }

    /** @test */
    public function check_that_proxy_nginx_file_is_created_during_setup()
    {
        self::$container->get('App\Server\NginxServer')->setup();

        $this->assertFileEquals(__DIR__.'/TestFiles/nginxProxyHttpBlock.php', '/data/test/docker-setup/nginx/proxy/nginx.conf');
    }

    /** @test */
    public function check_the_domains_proxy_conf_with_ssl_is_created_correctly()
    {
        $this->runApp(['url' => 'dev.example.com'], ['yes', 'None']);

        $this->assertFileEquals(__DIR__.'/TestFiles/nginxProxyServerBlockWithSSL.php', '/data/test/docker-setup/nginx/proxy/sites-enabled/dev.example.com.conf');
    }

    /** @test */
    public function check_the_domains_proxy_conf_is_created_correctly()
    {
        $this->runApp(['url' => 'dev.example.com'], ['no', 'None']);

        $this->assertFileEquals(__DIR__.'/TestFiles/nginxProxyServerBlock.php', '/data/test/docker-setup/nginx/proxy/sites-enabled/dev.example.com.conf');
    }
}
