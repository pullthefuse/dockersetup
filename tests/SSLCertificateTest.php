<?php

namespace App\Tests;

class SSLCertificateTest extends AbstractTestCase
{
    /** @test */
    public function createSSLCertificate()
    {
        $output = $this->runApp(['url' => 'dev.example.com'], ['yes', '0', '0', 0, 'None']);

        $this->assertContains('Creating SSL certificates...', $output);
        $this->assertFileExists('/data/test/docker-setup/tls/dev.example.com.key');
        $this->assertFileExists('/data/test/docker-setup/tls/dev.example.com.crt');
    }

    /** @test */
    public function updateSSLCertificate()
    {
        $this->runApp(['url' => 'dev.example.com'], ['yes', '0', '0', 0, 'None']);
        $oldCertificate = file_get_contents('/data/test/docker-setup/tls/dev.example.com.crt');
        $output = $this->runApp(['url' => 'dev.example.com'], [], 'docker:ssl:update');
        $newCertificate = file_get_contents('/data/test/docker-setup/tls/dev.example.com.crt');

        $this->assertContains('Deleting old SSL certificates...', $output);
        $this->assertContains('Renewing SSL certificates...', $output);
        $this->assertNotEquals($oldCertificate, $newCertificate);
    }

    /** @test */
    public function when_updating_throw_docker_setup_exception_if_domain_doesnt_exist()
    {
        $this->expectException('App\Exception\DockerSetupException');
        $output = $this->runApp(['url' => 'dev.example.com'], [], 'docker:ssl:update');

        $this->assertContains('Domain doesn\'t exist', $output);
    }
}
