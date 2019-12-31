<?php

namespace App\Exception;

use Symfony\Component\Console\Exception\ExceptionInterface;

class DockerSetupException extends \RuntimeException implements ExceptionInterface
{
}
