<?php

namespace App\Command;

use App\Config;
use App\ConsoleStyle;
use App\FileManager;
use App\HostFile;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\StyleInterface;

class SSLStatusCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'docker:ssl:status';

    /**
     * @var FileManager
     */
    private FileManager $fileManager;

    /**
     * @var StyleInterface
     */
    private StyleInterface $io;

    /**
     * @var HostFile
     */
    private HostFile $hostFile;

    /**
     * SSLStatusCommand constructor.
     *
     * @param FileManager $fileManager
     * @param HostFile $hostFile
     * @param string|null $name
     */
    public function __construct(FileManager $fileManager, HostFile $hostFile, string $name = null)
    {
        $this->fileManager = $fileManager;
        $this->hostFile = $hostFile;

        parent::__construct($name);
    }

    /**
     * @inheritDoc
     */
    public function configure(): void
    {
        $this
            ->setDescription('domain certificates expiry status')
            ->setHelp('View expiry dates of each domain');
    }

    /**
     * @inheritDoc
     */
    public function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new ConsoleStyle($input, $output);
    }

    /**
     * @inheritDoc
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $lines = $this->hostFile->getDomains();
        preg_match_all('/ (.+)/', $lines, $domains);

        $rootDirectory = Config::get('rootDirectory');

        $domains = array_map(function($domain) use ($rootDirectory) {
            $timestamp = openssl_x509_parse($this->fileManager->getFileContents("{$rootDirectory}/tls/{$domain}.crt"));
            $expiryDate = new \DateTime('@' . $timestamp['validTo_time_t']);

            return $domain.' - Certificate Expires: '.$expiryDate->format('d/m/Y');
        }, $domains[1]);

        $this->io->listing($domains);

        return 0;
    }
}
