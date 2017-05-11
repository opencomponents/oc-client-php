<?php

namespace OpenComponents;

use GuzzleHttp\Client as GuzzleClient;
use OpenComponents\Model\Component;

class ComponentDataRetriever
{
    private $components;

    private $httpClient;

    public function __construct($config)
    {
        foreach ($config['components'] as $component) {
            $this->components[] = [
                'name' => $component
            ];
        }

        $this->httpClient = new GuzzleClient([
            'base_uri' => $config['registries']['serverRendering']
        ]);
    }

    public function performRequest($components = null)
    {
        if (!$components) {
            $components = $this->components;
        }

        if (1 < count($components)) {
            $compsArr = [];
            $response = json_decode($this->performPost($components));

            if (!$response) {
                return;
            }

            foreach ($response as $comp) {
                $compsArr[] = $comp->response->html;
            }

            return $compsArr;
        }

        $response = json_decode($this->performGet(
            new Component($components[0]['name'])
        ));

        if ($response) {
            return $response->html;
        }

        return;
    }

    /**
     * performGet
     *
     * @param Component $component
     * @access public
     * @return Object
     */
    public function performGet(Component $component)
    {
        $response = $this->httpClient->get($component->getName());
        return (string) $response->getBody();
    }

    /**
     * performPost
     *
     * @param array $components
     * @access public
     * @return void
     */
    public function performPost(array $components)
    {
        $response = $this->httpClient->post('', [
            'components' => $components
        ]);

        return (string) $response->getBody();
    }

    /**
     * setHttpClient
     *
     * @param GuzzleClient $httpClient
     * @access public
     * @return ComponentDataRetriever
     */
    public function setHttpClient(GuzzleClient $httpClient)
    {
        $this->httpClient = $httpClient;

        return $this;
    }
}
