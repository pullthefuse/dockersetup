<?php

namespace App;

use App\Helper\Str;
use Symfony\Component\Yaml\Yaml;

class Config
{
    /**
     * @var array|null
     */
    private static $params;

    /**
     * Parse parameters file.
     */
    public static function setup(): void
    {
        $params = Yaml::parseFile(__DIR__.'/../config/'.(getenv('CONFIG_PATH') ?? '').'parameters.yaml')['parameters'];
        $parameterBag = new ParameterBag($params);
        $parameterBag->resolve();
        self::$params = $parameterBag->all();
    }

    /**
     * Get all parameters.
     *
     * @return array
     */
    public static function all(): array
    {
        if (null === self::$params) {
            self::setup();
        }

        return self::$params;
    }

    /**
     * Return parameter.
     *
     * @param $value
     * @return mixed|string
     */
    public static function get($value)
    {
        if (null === self::$params) {
            self::setup();
        }

        $values = explode('.', $value);

        $values = array_map(function($value) {
            return Str::snake($value);
        }, $values);

        $data = self::$params;

        foreach ($values as $value) {
            $data = $data[$value] ?? '';
        }

        return $data;
    }
}
