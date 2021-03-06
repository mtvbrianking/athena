<?php
declare(strict_types = 1);

namespace Athena;

use Athena\Factory\Factory;
use Athena\Handler\CallableHandler;
use Athena\Handler\RequestHandler;
use Closure;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use UnexpectedValueException;

class Dispatcher
{
    /**
     * @var MiddlewareInterface[]
     */
    private $stack;

    /**
     * Static helper to create and dispatch a request.
     *
     * @param array $stack
     * @param \Psr\Http\Message\ServerRequestInterface|null $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public static function run(array $stack, ServerRequestInterface $request = null): ResponseInterface
    {
        if ($request === null) {
            $request = Factory::createServerRequest('GET', '/');
        }

        return (new static($stack))->dispatch($request);
    }

    /**
     * @param MiddlewareInterface[] $stack middleware stack (with at least one middleware component)
     */
    public function __construct(array $stack)
    {
        $this->stack = $stack;
    }

    /**
     * Dispatches the middleware stack and returns the resulting `ResponseInterface`.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function dispatch(ServerRequestInterface $request): ResponseInterface
    {
        $resolved = $this->resolve(0);

        return $resolved->handle($request);
    }

    /**
     * Create Request handler from middleware.
     *
     * @param int $index middleware stack index
     *
     * @return RequestHandlerInterface
     */
    private function resolve(int $index): RequestHandlerInterface
    {
        return new RequestHandler(function (ServerRequestInterface $request) use ($index) {
            $middleware = isset($this->stack[$index]) ? $this->stack[$index] : new CallableHandler(function () {
            });

            if ($middleware instanceof Closure) {
                // Create middleware from closure
                $middleware = new CallableHandler($middleware);
            }

            if (!($middleware instanceof MiddlewareInterface)) {
                throw new UnexpectedValueException(
                    sprintf('The middleware must be an instance of %s', MiddlewareInterface::class)
                );
            }

            return $middleware->process($request, $this->resolve($index + 1));
        });
    }
}
