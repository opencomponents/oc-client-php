<?php

namespace OpenComponents\Model;

class Component
{
    private $name;

    private $version;

    private $parameters;

    private $container;

    private $render;

    public function __construct(
        $name,
        $version = '',
        $parameters = [],
        $container = false,
        $render = ''
    ) {
        $this->name = $name;
        $this->version = $version;
        $this->parameters = $parameters;
        $this->container = $container;
        $this->render = $render;
    }

    /**
     * getName
     *
     * @access public
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * getVersion
     *
     * @access public
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * getParameters
     *
     * @access public
     * @return void
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}
