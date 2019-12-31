<?php

namespace App\SSL;

use Symfony\Component\Console\Style\StyleInterface;

interface SSLInterface
{
    public function setup(StyleInterface $io, string $domain): bool;

    public function delete(string $domain): void;
}
