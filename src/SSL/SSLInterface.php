<?php

namespace App\SSL;

use Symfony\Component\Console\Style\StyleInterface;

interface SSLInterface
{
    /**
     * Creates a domains certificates.
     *
     * @param StyleInterface $io
     * @param string $domain
     * @return bool
     */
    public function setup(StyleInterface $io, string $domain): bool;

    /**
     * Updates a domains certificates
     *
     * @param StyleInterface $io
     * @param string $domain
     */
    public function update(StyleInterface $io, string $domain): void;

    /**
     * Deletes a domains certificates
     * @param string $domain
     */
    public function delete(string $domain): void;
}
