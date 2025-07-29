<?php
// public/index.php

use App\Kernel;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;

// 1) Charge l’autoloader et votre .env (via config/bootstrap.php)
require dirname(__DIR__).'/config/bootstrap.php';

// vérifier la variable/:
// echo '<pre>', var_dump('DATABASE_URL', getenv('DATABASE_URL')), '</pre>';

$env = $_SERVER['APP_ENV'] ?? 'dev';
$debug = (bool) ($_SERVER['APP_DEBUG'] ?? ('prod' !== $env));



if ($debug) {
    Debug::enable();
}

$kernel = new Kernel($env, $debug);
$request  = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);




