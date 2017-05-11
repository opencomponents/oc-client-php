<?php

namespace OpenComponents;

use OpenComponents\Model\Component;
use OpenComponents\ComponentDataRetriever;

class Client
{
    private $componentDataRetriever;

    /**
     * __construct
     *
     * @param array $registries
     * @param mixed $components
     * @param array $cache
     * @access public
     * @return void
     */
    public function __construct(array $registries, array $components, $cache = null)
    {
        $config['registries'] = $registries;
        $config['components'] = $components;

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
