<?php

namespace App\Tests;

use App\Server\NginxServer;

class ProxyTest extends AbstractTestCase
{
    /** @test */
    public function check_that_proxy_docker_compose_is_created_during_setup(): void
    {
        $this->runApp(['domain' => 'dev.example.com'], ['yes', 'no', '0', '0', '0', 'None']);
        self::$container->get(NginxServer::class)->setup();

        $this->assertFileEquals(__DIR__.'/TestFiles/dockerComposeProxy.yaml', getenv('DIR').'/docker/config/proxy.yaml');
    }

    /** @test */
    public function check_that_proxy_nginx_file_is_created_during_setup(): void
    {
        self::$container->get(NginxServer::class)->setup();

        $this->assertFileEquals(__DIR__.'/TestFiles/nginxProxyHttpBlock.php', getenv('DIR').'/nginx/proxy/nginx.conf');
    }

    /** @test */
    public function check_the_domains_proxy_conf_with_ssl_is_created_correctly(): void
    {
        $this->runApp(['domain' => 'dev.example.com'], ['yes', 'no', '0', '0', 0, 'None']);

        $this->assertFileEquals(__DIR__.'/TestFiles/nginxProxyServerBlockWithSSL.php', getenv('DIR').'/nginx/proxy/sites-enabled/dev.example.com.conf');
    }

    /** @test */
    public function check_the_domains_proxy_conf_is_created_correctly(): void
    {
        $this->runApp(['domain' => 'dev.example.com'], ['no', 'no', '0', '0', 0, 'None']);

        $this->assertFileEquals(__DIR__.'/TestFiles/nginxProxyServerBlock.php', getenv('DIR').'/nginx/proxy/sites-enabled/dev.example.com.conf');
    }
}
