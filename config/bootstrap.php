<?php
// config/bootstrap.php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

// Charge systématiquement .env et .env.local
(new Dotenv())->loadEnv(dirname(__DIR__).'/.env');
