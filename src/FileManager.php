<?php

namespace App;

use App\Exception\DockerSetupException;
use Symfony\Component\Filesystem\Filesystem;

class FileManager extends Filesystem
{
    /**
     * @var array
     */
    protected static array $trash = [];

    /**
     * Create the file with content.
     *
     * @param $file
     * @param $content
     */
    public function createFileContent($file, $content): void
    {
        if (!$this->isAbsolutePath($file)) {
             $file = Config::get('rootDirectory').'/'.$file;
        }

        $this->dumpfile($file, $content);
    }

    /**
     * Get file contents.
     *
     * @param string $path
     * @return string
     */
    public function getFileContents(string $path): string
    {
        if (!$this->exists($path)) {
            throw new \InvalidArgumentException(sprintf('Cannot find file "%s"', $path));
        }

        return file_get_contents($path);
    }

    /**
     * Check if file exists.
     *
     * @param iterable|string $file
     * @return bool
     */
    public function exists($file)
    {
        if (!$this->isAbsolutePath($file)) {
            $file = Config::get('rootDirectory').'/'.$file;
        }

        return parent::exists($file);
    }

    /**
     * Parse the template with the correct parameters.
     *
     * @param string $template
     * @param array $config
     * @return string
     */
    public function parseTemplate(string $template, array $config): string
    {
        ob_start();

        extract($config, EXTR_SKIP);

        include $template;

        return ob_get_clean();
    }

    /**
     * Add files to trash.
     *
     * @param $file
     */
    public function addToTrash($file)
    {
        if ($this->exists($file)) {
            self::$trash[] = $file;
        }
    }

    /**
     * Delete all files.
     */
    public function emptyTrash(): void
    {
        if (empty(self::$trash)) {
            throw new DockerSetupException('There are no files to delete...');
        }

        foreach (self::$trash as $file) {
            $this->remove($file);
        }

        self::$trash = [];
    }
}
