<?php

/*
|--------------------------------------------------------------------------
| Autoload php composer
|--------------------------------------------------------------------------
*/

require __DIR__.'/../vendor/autoload.php';

use Aura\Router\RouterContainer;
use Middlewares\Utils\Dispatcher;
use Middlewares\AuraRouter;
use Middlewares\RequestHandler;
use Narrowspark\HttpEmitter\SapiEmitter;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\ServerRequestFactory;

/*
|--------------------------------------------------------------------------
| Routing
|--------------------------------------------------------------------------
*/

$routerContainer = new RouterContainer();
$map = $routerContainer->getMap();

// $map->get('index', '/', [
//     \App\Http\Middlewares\Middleware1::class,
//     // new \App\Http\Middlewares\Middleware2($content),
//     \App\Http\Controllers\Controller::class,
// ]);

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

$dispatcher = new Dispatcher([
    new AuraRouter($routerContainer),
    new RequestHandler()
]);

$response = $dispatcher->dispatch($request);

# Post routing

$response->withHeader('X-Developed-By', 'bmatovu');

/*
|--------------------------------------------------------------------------
| Respond
|--------------------------------------------------------------------------
*/

$emitter = new SapiEmitter();
$emitter->emit($response);
