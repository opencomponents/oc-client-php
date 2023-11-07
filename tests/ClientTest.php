<?php

use PHPUnit\Framework\TestCase;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Client as GuzzleClient;

use OpenComponents\Client;
use OpenComponents\ComponentDataRetriever;

class ClientTest extends TestCase
{
    public function testConstructor()
    {
        // Testing options initialization
        $client = new Client([
            "serverRendering" => "https://some-registry.com"
        ], [
            "hello" => '1.2.3',
            "world" => '~2.2.2',
            "nover" => ''
        ]);

        $this->assertInstanceOf(
            Client::class,
            $client
        );
    }

    public function testRenderComponents()
    {
        $config = [
            "registries" => [
                "serverRendering" => "https://some-registry.com"
            ],
            "components" => [
                "hello" => '1.2.3',
                "world" => '~2.2.2'
            ]
        ];
        // Instance initialization
        $client = new Client($config['registries'], $config['components']);

        $client->setComponentDataRetriever(
            $this->mockComponentDataRetriever($config)
        );

        $components = $client->renderComponents([
            [
                "name" => "oc-client"
            ]
        ]);


        $this->assertNotNull($components);
        $this->assertTrue(isset($components['errors']));
        $this->assertTrue(is_array($components['errors']));
        $this->assertTrue(isset($components['html']));
        $this->assertTrue(is_string($components['html']));

        $components = $client->renderComponents([
            [
                "name" => "oc-client"
            ],
            [
                "name" => "world"
            ]
        ]);

        $this->assertEquals(2, count($components['html']));
        $this->assertMatchesRegularExpression('/script/', $components['html'][0]);
    }

    public function mockComponentDataRetriever($config)
    {
        $componentResponse = '{
            "type": "oc-component",
            "version": "0.36.15",
            "requestVersion": "",
            "name": "oc-client",
            "renderMode": "rendered",
            "href": "https://some-repo/oc-client",
            "html": "<script src=\"//s3-eu-west-1.amazonaws.com/somebucket/components/oc-client/0.36.15/src/oc-client.min.js\" type=\"text/javascript\"></script>"
        }';

        $componentsResponse = '[
        {
            "status": 200,
            "headers": {},
            "response": {
              "type": "oc-component",
              "version": "0.36.15",
              "requestVersion": "",
              "name": "oc-client",
              "renderMode": "rendered",
              "href": "https://some-repo/oc-client",
              "html": "<script src=\"//s3-eu-west-1.amazonaws.com/somebucket/components/oc-client/0.36.15/src/oc-client.min.js\" type=\"text/javascript\"></script>"
            }
          },
          {
            "status": 200,
            "headers": {},
            "response": {
              "type": "oc-component",
              "version": "0.9.0",
              "requestVersion": "",
              "name": "some-widget",
              "renderMode": "rendered",
              "href": "https://some-repo/some-widget",
              "html": "whatever"
            }
          }
        ]';

        $mock = new MockHandler([
            new Response(200, [], $componentResponse),
            new Response(200, [], $componentsResponse),
            new Response(200, [], $componentResponse)
        ]);

        $handler = HandlerStack::create($mock);
        $httpClient = new GuzzleClient([
            'handler' => $handler
        ]);
        $componentDataRetriever = new ComponentDataRetriever($config);
        return $componentDataRetriever->setHttpClient($httpClient);
    }
}
