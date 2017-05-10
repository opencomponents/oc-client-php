<?php

use PHPUnit\Framework\TestCase;

use OpenComponents\Client;
use GuzzleHttp\Client as GuzzleClient;

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

        $components = $client->renderComponents([
            [
                "name" => "hello"
            ]
        ]);

        $this->assertNotNull($components);
        $this->assertTrue(isset($components['errors']));
        $this->assertTrue(is_array($components['errors']));
        $this->assertTrue(isset($components['html']));
        $this->assertTrue(is_array($components['html']));
        $this->assertEquals(1, count($components['html']));

        // Mocking http client
        $httpClient = $this->mockingRenderComponentsClient();
        $client->setHttpClient($httpClient);

        $components = $client->renderComponents([
            [
                "name" => "hello"
            ],
            [
                "name" => "world"
            ]
        ]);

        $this->assertEquals(2, count($components['html']));

    }

    private function mockingRenderComponentsClient()
    {
        return new GuzzleClient();
    }
}
