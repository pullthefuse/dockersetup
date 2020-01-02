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
}
