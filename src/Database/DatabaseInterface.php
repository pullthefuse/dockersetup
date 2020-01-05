<?php

namespace App\Database;

interface DatabaseInterface
{
    /**
     * Create the database mapping config file
     */
    public function setup(): void;
}
