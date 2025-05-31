<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

if (file_exists(dirname(__DIR__).'/.env')) {
    $env = $_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? 'dev';

    if (in_array($env, ['dev', 'test'])) {
        (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
    }
}
