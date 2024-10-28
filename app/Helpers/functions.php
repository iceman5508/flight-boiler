<?php

function env(string $key,string $default = null)
{
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../..');
    $dotenv->load();
    return $_ENV[$key] ?? $default;
}