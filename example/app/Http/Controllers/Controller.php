<?php

namespace App\Http\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;

class Controller {

    public function __invoke(ServerRequestInterface $request) : ResponseInterface
    {
        $name = $request->getQueryParams()['name'] ?? 'mysterious';

        return new HtmlResponse('Hello, ' . $name . ' from controller!', 200);
    }

}
