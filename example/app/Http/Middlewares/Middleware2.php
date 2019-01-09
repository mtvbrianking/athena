<?php

namespace App\Http\Middlewares;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class Middleware2 implements MiddlewareInterface
{
    private $content;

    public function __construct($content = "App MW #2")
    {
        $this->content = $content;
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        echo "@app-mw-2 :: before<br/>";
        $response = $handler->handle($request);
        echo "@app-mw-2 :: after<br/>";
        $response->getBody()->write("<br/>".$this->content."<br/>");
        return $response;
    }
}
