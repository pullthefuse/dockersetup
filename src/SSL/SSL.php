<?php

namespace App\SSL;

use App\Config;
use App\Exception\DockerSetupException;
use App\FileManager;
use App\Server\ServerInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\StyleInterface;

class SSL implements SSLInterface
{
    /**
     * @var FileManager
     */
    private FileManager $fileManager;

    /**
     * @var ServerInterface
     */
    private ServerInterface $server;

    /**
     * SSL constructor.
     *
     * @param FileManager $fileManager
     * @param ServerInterface $server
     */
    public function __construct(FileManager $fileManager, ServerInterface $server)
    {
        $this->fileManager = $fileManager;
        $this->server = $server;
    }

    /**
     * @inheritDoc
     */
    public function setup(StyleInterface $io, string $domain): bool
    {
        $sslQuestion = new ConfirmationQuestion('Do you want to set up SSL?');

        $this->server->setSSL($ssl = $io->askQuestion($sslQuestion));

        if ($ssl) {
            $io->text('Creating SSL certificates...');
            $this->createCertificates($domain);
        }

        return $ssl;
    }

    /**
     * @inheritDoc
     */
    public function update(StyleInterface $io, string $domain): void
    {
        $rootDirectory = Config::get('rootDirectory');

        if (!$this->fileManager->exists("{$rootDirectory}/docker/config/{$domain}.yaml")) {
            throw new DockerSetupException('Domain doesn\'t exist. Aborting...');
        }

        $io->text('Deleting old SSL certificates...');
        $this->delete($domain);
        $io->text('Renewing SSL certificates...');
        $this->createCertificates($domain);
    }

    /**
     * @inheritDoc
     */
    public function delete($domain): void
    {
        $this->fileManager->addToTrash(Config::get('rootDirectory').'/tls/'.$domain.'.crt');
        $this->fileManager->addToTrash(Config::get('rootDirectory').'/tls/'.$domain.'.key');
    }

    /**
     * Create the certificates for the requested domain.
     *
     * @param $domain
     */
    public function createCertificates($domain): void
    {
        $config = [
            "digest_alg" => "AES-128-CBC",
            "private_key_bits" => 4096,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        ];

        $dn = ['commonName' => $domain];

        $key = openssl_pkey_new($config);
        $csr = openssl_csr_new($dn, $key, $config);
        $cert = openssl_csr_sign($csr, null, $key, 3650, $config);

        openssl_pkey_export($key, $keyString);
        openssl_x509_export($cert, $certString);

        $this->fileManager->createFileContent("tls/{$domain}.key", $keyString);
        $this->fileManager->createFileContent("tls/{$domain}.crt", $certString);
    }
}
