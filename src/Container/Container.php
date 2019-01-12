<?php

namespace Athena\Container;

use Pimple\Container as PimpleContainer;
use Psr\Container\ContainerInterface;

class Container extends PimpleContainer implements ContainerInterface
{
    public function __construct(array $values = [])
    {
        parent::__construct($values);
    }

    public function get($id) : mixed
    {
        if (!$this->offsetExists($id)) {
            // NotFoundExceptionInterface
            throw new \Exception(sprintf('Identifier "%s" is not defined on container.', $id));
        }
        try {
            return $this->offsetGet($id);
        } catch(\InvalidArgumentException $exception) {
            // ContainerExceptionInterface
            throw new \Exception(sprintf('Error while retrieving the entry: "%s".', $id));
        }
    }

    public function has($id) : bool
    {
        return $this->offsetExists($id);
    }
}
