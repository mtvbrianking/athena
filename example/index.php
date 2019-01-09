<?php

/*
|--------------------------------------------------------------------------
| Autoload php composer
|--------------------------------------------------------------------------
*/

require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Routing
|--------------------------------------------------------------------------
*/

$routerContainer = new \Aura\Router\RouterContainer();
$map = $routerContainer->getMap();

$map->get('index', '/', \App\Http\Controllers\Controller::class);

$router = new \Athena\Http\Router\AuraRouterAdaptor($routerContainer);

$request = \Zend\Diactoros\ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);

try {

    // .. and try to match the request to a route.
    $routeResult = $router->match($request);

    // add route attributes to the request
    foreach ($routeResult->getAttributes() as $key => $val) {
        $request = $request->withAttribute($key, $val);
    }

    $resolver = new \Athena\Http\HandlerResolver();
    $action = $resolver->resolve($routeResult->getHandler());
    $response = $action($request);
} catch (Exception $e) {
    $response = new \Zend\Diactoros\Response\JsonResponse([
        'error' => $e->getMessage(),
        $e->getCode(),
    ]);
}

# Post routing

$response->withHeader('X-Developed-By', 'bmatovu');

/*
|--------------------------------------------------------------------------
| Respond
|--------------------------------------------------------------------------
*/

var_dump($response);
