<?php

function env_or(string $key, string $default): string
{
    if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') {
        return (string) $_SERVER[$key];
    }
    $value = getenv($key);
    if ($value === false || $value === '') {
        return $default;
    }
    return (string) $value;
}

function db_config(): array
{
    return [
        'host' => env_or('DB_HOST', 'localhost'),
        'user' => env_or('DB_USER', 'root'),
        'pass' => env_or('DB_PASS', ''),
        'name' => env_or('DB_NAME', 'messagerie'),
    ];
}
