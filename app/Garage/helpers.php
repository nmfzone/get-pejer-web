<?php

use Carbon\Carbon;
use Jenssegers\Date\Date;

if (! function_exists('parse_date')) {
    /**
     * Parse the given date.
     *
     * @param  string|null  $value
     * @param  mixed  $default
     * @param  mixed  $format
     * @param  mixed  $toFormat
     * @return \Carbon\Carbon|string|null
     */
    function parse_date($value, $default = null, $format = true, $toFormat = true)
    {
        $format = $format === true ? 'Y-m-d H:i:s' : $format;
        $toFormat = $toFormat === true ? 'Y-m-d H:i:s' : $toFormat;

        if (empty($value) && empty($default)) {
            return null;
        }

        try {
            if (empty($value)) {
                if ($default instanceof Carbon && is_string($toFormat)) {
                    return Date::parse($default)->format($toFormat);
                }

                return $default;
            }

            if ($value instanceof Carbon) {
                $result = Date::parse($value);
            } else {
                $result = Date::createFromFormat($format, $value);
            }
        } catch (Exception $e) {
            return null;
        }

        return $toFormat ? $result->format($toFormat) : $result;
    }
}

if (! function_exists('join_paths')) {
    /**
     * Handle the path joins.
     *
     * @return string
     *
     * @throws Exception
     */
    function join_paths()
    {
        if (func_num_args() < 2) {
            throw new \Exception('join_paths() require atleast 2 arguments!');
        }

        $args = func_get_args();
        $paths = [];

        foreach ($args as $arg) {
            $paths = array_merge($paths, (array) $arg);
        }

        foreach ($paths as &$path) {
            $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
        }

        $firstPath = rtrim($paths[0], DIRECTORY_SEPARATOR);

        $paths = array_map(function ($p) {
            return trim($p, sprintf('"%s"', DIRECTORY_SEPARATOR));
        }, array_slice($paths, 1));

        $paths = array_merge([$firstPath], $paths);

        return join(DIRECTORY_SEPARATOR, $paths);
    }
}

if (! function_exists('join_url')) {
    /**
     * Handle the url joins.
     *
     * @return string
     *
     * @throws Exception
     */
    function join_url()
    {
        $args = func_get_args();
        $paths = [];

        foreach ($args as $arg) {
            $paths = array_merge($paths, (array) $arg);
        }

        $paths = array_map(function ($p) {
            return trim($p, sprintf('"%s"', '/'));
        }, $paths);

        return join('/', $paths);
    }
}

if (! function_exists('ddd')) {
    /**
     * Dump the passed variables and end the script.
     *
     * @param  mixed  $args
     * @return void
     */
    function ddd(...$args)
    {
        http_response_code(500);

        call_user_func_array('dd', $args);
    }
}
