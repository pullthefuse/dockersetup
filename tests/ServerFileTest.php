<?php

namespace App\Tests;

use App\Server\NginxServer;

class ServerFileTest extends AbstractTestCase
{
    /** @test */
    public function initialise_nginx_for_the_first_time_and_setup_returns_false(): void
    {
        $server = self::$container->get(NginxServer::class);

        $this->assertFalse($server->isSetupComplete());
    }

    /** @test */
    public function create_nginx_http_block(): void
    {
        self::$container->get(NginxServer::class)->setup();

        $this->assertFileEquals(__DIR__.'/TestFiles/nginxHttpBlock.php', getenv('DIR').'/nginx/nginx.conf');
    }

    /** @test */
    public function create_nginx_server_block_for_non_ssl_only(): void
    {
        $this->runApp(['domain' => 'dev.example.com'], ['no', 'no', '0', '0', 0, 'None']);

        $this->assertFileEquals(__DIR__.'/TestFiles/nginxServerBlock.php', getenv('DIR').'/nginx/sites-enabled/dev.example.com.conf');
    }

    /** @test */
    public function create_nginx_server_block_for_ssl(): void
    {
        $this->runApp(['domain' => 'dev.example.com'], ['yes', 'no', '0', '0', 0, 'None']);

        $this->assertFileEquals(__DIR__.'/TestFiles/nginxServerBlockWithSSL.php', getenv('DIR').'/nginx/sites-enabled/dev.example.com.conf');
    }
}
