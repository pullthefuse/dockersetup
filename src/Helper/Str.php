<?php

namespace App\Helper;

class Str
{
    /**
     * @var array
     */
    private static array $snakeCache = [];

    /**
     * @var array
     */
    private static array $slugCache = [];

    /**
     * Convert a string to snake case.
     *
     * @param  string  $value
     * @param  string  $delimiter
     * @return string
     */
    public static function snake($value, $delimiter = '_'): string
    {
        $key = $value;

        if (isset(self::$snakeCache[$key][$delimiter])) {
            return self::$snakeCache[$key][$delimiter];
        }

        if (! ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', $value);
            $value = self::lower(preg_replace('/(.)(?=[A-Z])/u', '$1'.$delimiter, $value));
        }

        return self::$snakeCache[$key][$delimiter] = $value;
    }

    /**
     * Convert the given string to lower-case.
     *
     * @param  string  $value
     * @return string
     */
    public static function lower($value): string
    {
        return mb_strtolower($value, 'UTF-8');
    }

    /**
     * Generate a URL friendly "slug" from a given string.
     *
     * @param  string  $title
     * @param  string  $separator
     * @return string
     */
    public static function slug(string $title, string $separator = '_'): string
    {
        if (isset(self::$slugCache[$title])) {
            return self::$slugCache[$title];
        }

        $key = $title;
        // Convert all dashes/underscores into separator
        $flip = $separator === '_' ? '-' : '_';
        $title = preg_replace('!['.preg_quote($flip, '/').']+!u', $separator, $title);
        // Replace @ with the word 'at'
        // Replace . with separator
        $title = str_replace(['@', '.'], [$separator . 'at' . $separator, $separator], $title);
        // Remove all characters that are not the separator, letters, numbers, or whitespace.
        $title = preg_replace('![^'.preg_quote($separator, '/').'\pL\pN\s]+!u', '', static::lower($title));
        // Replace all separator characters and whitespace by a single separator
        $title = preg_replace('!['.preg_quote($separator, '/').'\s]+!u', $separator, $title);

        return self::$slugCache[$key] = trim($title, $separator);
    }
}
