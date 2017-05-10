<?php

use PHPUnit\Framework\TestCase;

use OpenComponents\Client;

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

    public function renderComponentsTest()
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

        $client->renderComponents();
    }
}
