<?php

/*
|--------------------------------------------------------------------------
| Autoload php composer
|--------------------------------------------------------------------------
*/

require __DIR__.'/../../vendor/autoload.php';

use Athena\Container\Container;
use Athena\Container\RequestHandlerContainer;

use Athena\Dispatcher;

use Athena\Middleware\AuraRouter;
use Athena\Middleware\RequestHandler;
use Athena\Middleware\Whoops;

use Athena\ResponseEmitter;

use App\Http\Controllers\Controller;

use App\Http\Middlewares\Middleware1;
use App\Http\Middlewares\Middleware2;

use Aura\Router\RouterContainer;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// use Narrowspark\HttpEmitter\SapiEmitter;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Twig\Loader\FilesystemLoader; // as Twig_Loader_Filesystem
use Twig\Environment; // as Twig_Environment

use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\ServerRequestFactory;

/*
|--------------------------------------------------------------------------
| DI Container
|--------------------------------------------------------------------------
*/

$container = new Container();

if (!isset($container['request'])) {
    $container['request'] = function ($container) {
        return ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
    };
}

if (!isset($container['logger'])) {
    $container['logger'] = function($container) {
        $logger = new Logger('Debugger');
        $file_handler = new StreamHandler(__DIR__.'../../logs/debug.log');
        $logger->pushHandler($file_handler);
        return $logger;
    };
}

if (!isset($container['twig'])) {
    $container['twig'] = function () {
        $loader = new FilesystemLoader(__DIR__.'../../views');
        return new Environment($loader, [
            'cache' => __DIR__.'../../views/cache',
            'auto_reload' => true,
        ]);
    };
}

/*
|--------------------------------------------------------------------------
| Routing
|--------------------------------------------------------------------------
*/

$routerContainer = new RouterContainer();
$map = $routerContainer->getMap();

$map->get('index', '/', Controller::class.'::index');

$map->get('invoke', '/invoke', Controller::class);

$map->post('edit', '/edit/{id}', Controller::class.'::edit')->tokens([
    'id' => '\d+',
]);

$map->get('greet', '/greet/{name}', function (ServerRequestInterface $request) : ResponseInterface {
    $name = $request->getAttribute('name');

    return new HtmlResponse('Hello, ' . $name . ' from closure!', 200);
});

/*
|--------------------------------------------------------------------------
| Middleware
|--------------------------------------------------------------------------
*/

$request = ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);

$reqContainer = new RequestHandlerContainer([
    'container' => $container,
]);

$dispatcher = new Dispatcher([
    new Whoops,
    new Middleware1(),
    new AuraRouter($routerContainer),
    new RequestHandler($reqContainer),
]);

$response = $dispatcher->dispatch($request);

# Post routing

$response->withHeader('X-Developed-By', 'bmatovu');

/*
|--------------------------------------------------------------------------
| Respond
|--------------------------------------------------------------------------
*/

// $emitter = new SapiEmitter();
$emitter = new ResponseEmitter();
$emitter->emit($response);

/*
|--------------------------------------------------------------------------
| Dependencies
|--------------------------------------------------------------------------
*/

// psr/container
// psr/http-server-response
// psr/http-server-middleware
// psr/http-server-handler
// psr/http-message
// psr/http-factory
// psr/log

// aura/router
// filp/whoops
// monolog/monolog
// narrowspark/http-emitter
// pimple/pimple
// twig/twig
// zendframework/zend-diactoros
