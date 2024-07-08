<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

if (file_exists(dirname(__DIR__).'/.env')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

// Ensure that .env.test is loaded in test environment
if (isset($_SERVER['APP_ENV']) && 'test' === $_SERVER['APP_ENV']) {
    if (file_exists(dirname(__DIR__).'/.env.test')) {
        (new Dotenv())->overload(dirname(__DIR__).'/.env.test');
    }
}
