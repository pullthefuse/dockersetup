<?php

namespace App\Project;

class LaravelProject extends Project
{
    /**
     * @inheritDoc
     */
    public function configure(): void
    {
        $this
            ->setType('Laravel')
            ->setTimeout(420)
            ->addProcessArgument('--prefer-dist');
    }
}
