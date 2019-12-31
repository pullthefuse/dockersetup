<?php

namespace App;

use Symfony\Component\Console\Style\SymfonyStyle;

class ConsoleStyle extends SymfonyStyle
{
    /**
     * Display text.
     *
     * @param $message
     */
    public function text($message)
    {
        $this->block($message);
    }

    /**
     * Display error text.
     *
     * @param $message
     */
    public function errorText($message)
    {
        $this->block($message, null, 'fg=red;bg=default', '', true);
    }

    /**
     * Display error text.
     *
     * @param $message
     */
    public function greenText($message)
    {
        $this->block($message, null, 'fg=green;bg=default', '', true);
    }

    /**
     * Display error text.
     *
     * @param $message
     */
    public function outputText($message)
    {
        $this->block($message, null, 'fg=green;bg=default', '', true);
    }

    /**
     * Clear the console screen.
     */
    public function clearScreen()
    {
        $this->write(sprintf("\033\143"));
    }

    /**
     * Format the buffer output instead of using tty.
     *
     * @param $buffer
     */
    public function formatBuffer($buffer): void
    {
        $this->write($this->setColors($buffer));
    }

    /**
     * Format the various colors.
     *
     * @param string $buffer
     * @return string
     */
    public function setColors(string $buffer): string
    {
        foreach (['Installing', 'Configuring'] as $type) {
            $package = "/{$type}(.*?)\(/s";
            $version = '/\((.*?)\)/s';
            $ok = '/(\[OK\])/s';

            if (preg_match($ok, $buffer, $matches)) {
                $result = str_replace($matches[1], "<fg=green;bg=default>{$matches[1]}</>", $buffer);
                break;
            }

            if (!preg_match($package, $buffer, $matches)) {
                continue;
            }

            $buffer = str_replace($matches[1], "<fg=green;bg=default>{$matches[1]}</>", $buffer);

            if (!preg_match_all($version, $buffer, $matches)) {
                continue;
            }

            $result = end($matches[1]);
            $result = str_replace($result, "<fg=yellow;bg=default>{$result}</>", $buffer);
            break;
        }

        return $result ?? $buffer;
    }
}
