<?php

if (! function_exists('config')) {
    /**
     * @template T
     * Retrieve a configuration value from the config directory.
     *
     * @return T|null
     */
    function config(?string $key = null, ?string $default = null)
    {
        static $configCache = [];

        if ($key === null) {
            return $configCache;
        }

        if (isset($configCache[$key])) {
            return $configCache[$key];
        }

        $configDirectory = __DIR__ . '/src/config/';
        $segments = explode('.', $key);
        $fileName = array_shift($segments);
        $filePath = $configDirectory . $fileName . '.php';

        if (! file_exists($filePath)) {
            return $default;
        }

        $config = require $filePath;

        foreach ($segments as $segment) {
            if (! is_array($config) || !array_key_exists($segment, $config)) {
                return $default;
            }
            $config = $config[$segment];
        }

        $configCache[$key] = $config;

        return $config;
    }
}

if (! function_exists('env')) {
    function env(string $var, $default = null): ?string
    {
        return $_ENV[$var] ?? $default;
    }
}
