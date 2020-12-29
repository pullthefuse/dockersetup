<?php

namespace App\Tests;

use App\Exception\DockerSetupException;

class SSLCertificateTest extends AbstractTestCase
{
    /** @test */
    public function createSSLCertificate(): void
    {
        $output = $this->runApp(['domain' => 'dev.example.com'], ['yes', 'no', '0', '0', 0, 'None']);

        $this->assertStringContainsString('Creating SSL certificates...', $output);
        $this->assertFileExists(getenv('DIR').'/tls/dev.example.com.key');
        $this->assertFileExists(getenv('DIR').'/tls/dev.example.com.crt');
    }

    /** @test */
    public function updateSSLCertificate(): void
    {
        $this->runApp(['domain' => 'dev.example.com'], ['yes', 'no', '0', '0', 0, 'None']);
        $oldCertificate = file_get_contents(getenv('DIR').'/tls/dev.example.com.crt');
        $output = $this->runApp(['domain' => 'dev.example.com'], [], 'docker:ssl:update');
        $newCertificate = file_get_contents(getenv('DIR').'/tls/dev.example.com.crt');

        $this->assertStringContainsString('Deleting old SSL certificates...', $output);
        $this->assertStringContainsString('Renewing SSL certificates...', $output);
        $this->assertNotEquals($oldCertificate, $newCertificate);
    }

    /** @test */
    public function when_updating_throw_docker_setup_exception_if_domain_doesnt_exist(): void
    {
        $this->expectException(DockerSetupException::class);
        $output = $this->runApp(['domain' => 'dev.example.com'], [], 'docker:ssl:update');

        $this->assertStringContainsString('Domain doesn\'t exist', $output);
    }

    /** @test */
    public function view_domain_certificates_expiry_date(): void
    {
        $this->runApp(['domain' => 'dev.example.com'], ['yes', 'no', '0', '0', 0, 'None']);
        $this->runApp(['domain' => 'dev.example2.com'], ['yes', 'no', '0', '0', 0, 'None']);

        $output = $this->runApp([], [], 'docker:ssl:status');

        $this->assertStringContainsString('dev.example.com - Certificate Expires:', $output);
        $this->assertStringContainsString('dev.example2.com - Certificate Expires:', $output);
    }
}
