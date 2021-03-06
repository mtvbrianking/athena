<?php
declare(strict_types = 1);

namespace Athena\Middleware;

use Aura\Router\RouterContainer;
use Athena\Support\Traits\HasResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuraRouter implements MiddlewareInterface
{
    use HasResponseFactory;

    /**
     * @var RouterContainer The router container
     */
    private $router;

    /**
     * @var string Attribute name for handler reference
     */
    private $attribute = 'request-handler';

    /**
     * Set the RouterContainer instance.
     *
     * @param \Aura\Router\RouterContainer $router
     */
    public function __construct(RouterContainer $router)
    {
        $this->router = $router;
    }

    /**
     * Set the attribute name to store handler reference.
     *
     * @param string $attribute
     * @return \Athena\Middleware\AuraRouter
     */
    public function attribute(string $attribute): self
    {
        $this->attribute = $attribute;
        return $this;
    }

    /**
     * Process a server request and return a response.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Server\RequestHandlerInterface $handler
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $matcher = $this->router->getMatcher();
        $route = $matcher->match($request);

        if (!$route) {
            $failedRoute = $matcher->getFailedRoute();

            switch ($failedRoute->failedRule) {
                case 'Aura\Router\Rule\Allows':
                    return $this->createResponse(405)
                        ->withHeader('Allow', implode(', ', $failedRoute->allows)); // 405 METHOD NOT ALLOWED
                case 'Aura\Router\Rule\Accepts':
                    return $this->createResponse(406); // 406 NOT ACCEPTABLE
                case 'Aura\Router\Rule\Host':
                case 'Aura\Router\Rule\Path':
                    return $this->createResponse(404); // 404 NOT FOUND
                default:
                    return $this->createResponse(500); // 500 INTERNAL SERVER ERROR
            }
        }

        foreach ($route->attributes as $name => $value) {
            $request = $request->withAttribute($name, $value);
        }

        $request = $request->withAttribute($this->attribute, $route->handler);

        return $handler->handle($request);
    }
}
