<?php

namespace App\Project;

class SymfonyProject extends Project
{
    /**
     * @inheritDoc
     */
    public function configure(): void
    {
        $this
            ->setType('Symfony')
            ->setTimeout(300);
    }
}
