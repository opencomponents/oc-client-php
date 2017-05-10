<?php

namespace OpenComponents\Model;

class Component
{
    private $name;

    private $version;

    private $parameters;

    private $container;

    private $render;

    public function __construct($name, $version, $parameters, $container, $render)
    {
        $this->name = $name;
        $this->version = $version;
        $this->parameters = $parameters;
        $this->container = $container;
        $this->render = $render;
    }

    public function getVersion()
    {
        return $this->version;
    }
}
