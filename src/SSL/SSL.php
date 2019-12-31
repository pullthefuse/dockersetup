<?php

namespace App\SSL;

use App\Config;
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
     * Confirm whether SSL needs to be setup and run in.
     *
     * @param StyleInterface $io
     * @param string $domain
     * @return bool
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
     * Delete the certificates for a domain.
     *
     * @param $domain
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
