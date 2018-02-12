<?php
declare(strict_types=1);

use App\Kernel;
use Composer\Autoload\ClassLoader;
use Symfony\Component\Debug\Debug;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;

/** @var ClassLoader $loader */
$loader = require __DIR__.'/../vendor/autoload.php';

$env = $_SERVER['APP_ENV'] ?? 'dev';
if ($env === 'dev') {
    $dotenv = new Dotenv();
    $dotenv->load(__DIR__.'/../.env');
}

$debug = $_SERVER['APP_DEBUG'] ?? ($env !== 'prod');
if ($debug) {
    umask(0000);

    if (class_exists(Debug::class)) {
        /** @noinspection ForgottenDebugOutputInspection */
        Debug::enable();
    }
}

$trustedProxies = $_SERVER['TRUSTED_PROXIES'] ?? false;
if ($trustedProxies) {
    Request::setTrustedProxies(explode(',', $trustedProxies), Request::HEADER_X_FORWARDED_ALL ^ Request::HEADER_X_FORWARDED_HOST);
}

$trustedHosts = $_SERVER['TRUSTED_HOSTS'] ?? false;
if ($trustedHosts) {
    Request::setTrustedHosts(explode(',', $trustedHosts));
}

$kernel = new Kernel($env, $debug);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
