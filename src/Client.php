<?php

namespace OpenComponents;

use OpenComponents\Model\Component;
use OpenComponents\ComponentDataRetriever;

class Client
{
    private $config;

    private $componentDataRetriever;

    public function __construct($config)
    {
        $this->config = $config;

        $this->setComponentDataRetriever(
            new ComponentDataRetriever($config)
        );
    }

    /**
     * renderComponents
     *
     * @param mixed $components
     * @access public
     * @return array
     */
    public function renderComponents($components)
    {
        $renderedComponents = $this
            ->componentDataRetriever
            ->performRequest($components);

        return [
            'html' => $renderedComponents,
            'errors' => []
        ];
    }

    /**
     * setComponentDataRetriever
     *
     * @param ComponentDataRetriever $dataRetriever
     * @access public
     * @return void
     */
    public function setComponentDataRetriever(ComponentDataRetriever $dataRetriever)
    {
        $this->componentDataRetriever = $dataRetriever;
    }
}
