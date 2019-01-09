<?php

namespace Athena\Http;

class HandlerResolver
{
	public function resolve($handler) : callable
	{
		if(is_string($handler)) {
			return new $handler();
		}

		return $handler;
	}

	public function __invoke($handler) : callable
	{
		return $this->resolve($handler);
	}

}
