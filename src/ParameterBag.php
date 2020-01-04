<?php

namespace App;

use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag as SymfonyParameterBag;

class ParameterBag extends SymfonyParameterBag
{
    /**
     * {@inheritdoc}
     */
    public function get(string $name)
    {
        if (0 === strpos($name, 'env(') && ')' === substr($name, -1) && 'env()' !== $name) {
            $env = substr($name, 4, -1);

            if (!preg_match('/^(?:\w*+:)*+\w++$/', $env)) {
                throw new InvalidArgumentException(sprintf('Invalid %s name: only "word" characters are allowed.', $name));
            }
            if ($this->has($name) && null !== ($defaultValue = parent::get($name)) && !\is_string($defaultValue)) {
                throw new RuntimeException(sprintf('The default value of an env() parameter must be a string or null, but "%s" given to "%s".', \gettype($defaultValue), $name));
            }

            return getenv($env);
        }

        return parent::get($name);
    }
}
