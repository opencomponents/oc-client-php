<?php

namespace OpenComponents;

use GuzzleHttp\Client as GuzzleClient;
use OpenComponents\Model\Component;

class Client
{
    private $httpClient;

    public function __construct($config)
    {
        $this->httpClient = new GuzzleClient([
            'base_uri' => $config['registries']['serverRendering']
        ]);
    }

    public function renderComponents($components)
    {
        $renderedComponents = [];

        foreach ($components as $component) {
            $comp = new Component($component['name']);
            $response = $this->httpClient->get($comp->getName());
            $renderedComponents[] = (string) $response->getBody();
        }
        return [
            'html' => $renderedComponents,
            'errors' => []
        ];
    }

    public function setHttpClient(GuzzleClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }
}
