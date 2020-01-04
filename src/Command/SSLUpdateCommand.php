<?php

namespace App\Command;

use App\ConsoleStyle;
use App\SSL\SSLInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\StyleInterface;

class SSLUpdateCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'docker:ssl:update';

    /**
     * @var SSLInterface
     */
    private SSLInterface $ssl;

    /**
     * @var StyleInterface
     */
    private StyleInterface $io;

    /**
     * SSLRenewCommand constructor.
     *
     * @param SSLInterface $ssl
     * @param string|null $name
     */
    public function __construct(SSLInterface $ssl, string $name = null)
    {
        $this->ssl = $ssl;

        parent::__construct($name);
    }

    /**
     * @inheritDoc
     */
    public function configure(): void
    {
        $this
            ->setDescription('update SSL certificate')
            ->addArgument('domain', InputArgument::REQUIRED)
            ->setHelp('Update a domains ssl certificates');
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
        $this->ssl->update($this->io, $input->getArgument('domain'));

        return 0;
    }
}
