<?php

namespace App;

class HostFile
{
    /**
     * @var string
     */
    private const NAME = 'Docker Setup';

    /**
     * @var FileManager
     */
    private FileManager $fileManager;

    /**
     * @var string
     */
    protected string $blockStart;

    /**
     * @var string
     */
    protected string $blockEnd;

    /**
     * HostFile constructor.
     *
     * @param FileManager $fileManager
     */
    public function __construct(FileManager $fileManager)
    {
        $this->fileManager = $fileManager;
        $this->blockStart = sprintf('###> %s ###', self::NAME);
        $this->blockEnd = sprintf('###< %s ###', self::NAME);
    }

    /**
     * Check if domains can be added to host file and process.
     *
     * @param string $domain
     */
    public function addToHostFile(string $domain): void
    {
        if (!$this->fileManager->exists(Config::get('hostFile'))) {
            $this->fileManager->createFileContent(Config::get('hostFile'), '');
        }

        $this->add($domain);
    }

    /**
     * Decide whether to create file or update current one.
     *
     * @param $domain
     * @return bool
     */
    public function add($domain): bool
    {
        $ipAddress = Config::get('hostIpAddress');
        $data = "{$ipAddress} {$domain}";

        $contents = $this->fileManager->getFileContents(Config::get('hostFile'));

        if (false === strpos($contents, $this->blockStart) || false === strpos($contents, $this->blockEnd)) {
            return $this->createBlock($data);
        }

        return $this->updateBlock($data, $contents);
    }

    /**
     * Remove domain from hosts file.
     *
     * @param string $domain
     */
    public function removeDomain(string $domain): void
    {
        if ($this->fileManager->exists($hostFile = Config::get('rootDirectory').'/etc/hosts')) {
            $hostFileContent = $hostFile;
            $pattern = '/'.preg_quote(Config::get('hostIpAddress'), '/').' '.$domain.'/';
            $hostFileContent = preg_replace($pattern, '', $hostFileContent);
            $this->fileManager->dumpFile(Config::get('rootDirectory').'/etc/hosts', $hostFileContent);
        }
    }

    /**
     * Create a new block for host file.
     *
     * @param $data
     * @return bool
     */
    protected function createBlock($data): bool
    {
        $data = $this->buildBlock($data);

        $this->fileManager->appendToFile(Config::get('hostFile'), $data);

        return true;
    }

    /**
     * Update existing block with domain.
     *
     * @param $data
     * @param $contents
     * @return bool
     */
    protected function updateBlock($data, $contents): bool
    {
        $pattern = '/'.preg_quote($this->blockStart, '/').'.*?'.preg_quote($this->blockEnd, '/').'/s';

        $domains = $this->getDomains();

        $data = trim($domains)."\n".$data;

        $data = $this->buildBlock($data);

        $newContents = preg_replace($pattern, trim($data), $contents);
        $this->fileManager->dumpFile(Config::get('hostFile'), $newContents);

        return true;
    }

    public function getDomains()
    {
        $contents = $this->fileManager->getFileContents(Config::get('hostFile'));

        $pattern2 = '/'.preg_quote($this->blockStart, '/').'(.+)'.preg_quote($this->blockEnd, '/').'/s';

        preg_match($pattern2, $contents, $matches);

        return $matches[1] ?? [];
    }

    /**
     * Build the block with start and end lines.
     *
     * @param string $data
     * @return string
     */
    protected function buildBlock(string $data): string
    {
        return "\n".sprintf('###> %s ###%s%s%s###< %s ###%s', self::NAME, "\n", rtrim($data, "\r\n"), "\n", self::NAME, "\n");
    }
}
