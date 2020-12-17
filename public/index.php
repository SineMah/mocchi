<?php

use App\Kernel;
use App\Container;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\HttpFoundation\Request;

$loader = require __DIR__.'/../vendor/autoload.php';

define('APP_PATH', realpath(implode(DIRECTORY_SEPARATOR, [__DIR__, '..'])));

// auto-load annotations
AnnotationRegistry::registerLoader([$loader, 'loadClass']);

$dotenv = Dotenv\Dotenv::createImmutable(APP_PATH);
$dotenv->load();

$container = new Container();
//$container->middleware();

$kernel = new Kernel(env('ENVIRONMENT'), true);
$kernel->setContainer($container);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
//Fix for now
$container->middleware();
$response->send();
$kernel->terminate($request, $response);
