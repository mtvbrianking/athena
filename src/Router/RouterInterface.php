<?php

namespace Athena\Router;

use Psr\Http\Message\ServerRequestInterface;

interface RouterInterface
{
    /**
     * Match route.
     * @param  ServerRequestInterface $request
     * @throws Exception
     * @return RouteResult
     */
    public function match(ServerRequestInterface $request) : RouteResult;

    /**
     * Generate URI.
     * @param  string $name Route name
     * @param  array $params
     * @throws Exception
     * @return string
     */
    public function generateUri(string $name, array $params = []) : string;
}
