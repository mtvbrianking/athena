<?php

namespace Athena\Router;

class RouteResult
{
    /**
     * @var
     */
    private $name;

    /**
     * @var
     */
    private $handler;

    /**
     * @var array
     */
    private $attributes;

    /**
     * RouteResult constructor.
     *
     * @param $name
     * @param $handler
     * @param array $attributes
     */
    public function __construct($name, $handler, array $attributes)
    {
        $this->name = $name;
        $this->handler = $handler;
        $this->attributes = $attributes;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getHandler() : string
    {
        return $this->handler;
    }

    /**
     * @return array
     */
    public function getAttributes() : array
    {
        return $this->attributes;
    }
}
