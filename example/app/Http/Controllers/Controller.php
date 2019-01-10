<?php

namespace App\Http\Controllers;

use Pimple\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;

class Controller
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function __invoke(ServerRequestInterface $request) : ResponseInterface
    {
        // throw new \Exception("Testing whoops error handler", 500, null);

        $name = $request->getQueryParams()['name'] ?? 'mysterious';

        return new HtmlResponse('Hello, ' . $name . ' from controller!', 200);
    }

    public function index()
    {
        $this->container['logger']->debug("test", ["key" => "value"]);

        $raw = $this->container['twig']->render("index.twig", [
            "name" => "Minion",
        ]);
        var_dump($raw);
        die();
        return new HtmlResponse('You\'re at Controller@index!', 200);
    }

}
