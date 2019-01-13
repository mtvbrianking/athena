<?php

namespace Athena\Router;

use Aura\Router\Exception\RouteNotFound;
use Aura\Router\RouterContainer;
use Psr\Http\Message\ServerRequestInterface;

class AuraRouterAdaptor implements RouterInterface
{
    /**
     * @var \Aura\Router\RouterContainer
     */
	private $container;

    /**
     * AuraRouterAdaptor constructor.
     *
     * @param \Aura\Router\RouterContainer $container
     */
	public function __construct(RouterContainer $container)
	{
		$this->container = $container;
	}

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Athena\Router\RouteResult
     * @throws \Exception
     */
	public function match(ServerRequestInterface $request) : RouteResult
    {
        $matcher = $this->container->getMatcher();

        $route = $matcher->match($request);

        if($route) {
        	return new RouteResult($route->name, $route->handler, $route->attributes);
        }

        // get the first of the best-available non-matched routes
        $failedRoute = $matcher->getFailedRoute();

        // which matching rule failed?
        switch ($failedRoute->failedRule) {
            case 'Aura\Router\Rule\Allows':
                throw new \Exception("Not Allowed", 405, null);
                break;
            case 'Aura\Router\Rule\Accepts':
                throw new \Exception("Not Acceptable", 406, null);
                break;
            default:
                throw new \Exception("Not Found", 404, null);
                break;
        }
    }

    /**
     * @param string $name
     * @param array $params
     * @return string
     * @throws \Exception
     */
    public function generateUri(string $name, array $params = []) : string
    {
    	$generator = $this->container->getGenerator();
    	try{
    		return $generator->generate($name, $params);
    	} catch(RouteNotFound $ex) {
    		throw new \Exception("Route not found", null, null);
    	}
    }
}
