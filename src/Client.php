<?php

namespace OpenComponents;

class Client
{
    public function renderComponents($components)
    {
        return [
            'html' => $components,
            'errors' => []
        ];
    }

    public function setHttpClient(\GuzzleHttp\Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }
}
