<?php

use PHPUnit\Framework\TestCase;

use OpenComponents\Client;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class ClientTest extends TestCase
{
    public function testConstructor()
    {
        // Testing options initialization
        $client = new Client([
            "registries" => [
                "serverRendering" => "https://some-registry.com"
            ],
            "components" => [
                "hello" => '1.2.3',
                "world" => '~2.2.2',
                "nover" => ''
            ]
        ]);

        $this->assertInstanceOf(
            Client::class,
            $client
        );
    }

    public function testRenderComponents()
    {
        // Instance initialization
        $client = new Client([
            "registries" => [
                "serverRendering" => "https://some-registry.com"
            ],
            "components" => [
                "hello" => '1.2.3',
                "world" => '~2.2.2'
            ]
        ]);

        // Mocking http client
        $httpClient = $this->mockingRenderComponentsClient();
        $client->setHttpClient($httpClient);

        $components = $client->renderComponents([
            [
                "name" => "oc-client"
            ]
        ]);

        $this->assertNotNull($components);
        $this->assertTrue(isset($components['errors']));
        $this->assertTrue(is_array($components['errors']));
        $this->assertTrue(isset($components['html']));
        $this->assertTrue(is_array($components['html']));
        $this->assertEquals(1, count($components['html']));

        $components = $client->renderComponents([
            [
                "name" => "oc-client"
            ],
            [
                "name" => "world"
            ]
        ]);

        $this->assertEquals(2, count($components['html']));
        $this->assertRegexp('/script/', $components['html'][0]);

    }

    private function mockingRenderComponentsClient()
    {
        $componentsResponse = '<script src=\"//s3-eu-west-1.amazonaws.com/storage/components/oc-client/0.36.15/src/oc-client.min.js\" type=\"text/javascript\"></script>';
        $mock = new MockHandler([
            new Response(200, [], $componentsResponse),
            new Response(200, [], $componentsResponse),
            new Response(200, [], $componentsResponse)
        ]);

        $handler = HandlerStack::create($mock);
        return new GuzzleClient([
            'handler' => $handler
        ]);
    }
}
