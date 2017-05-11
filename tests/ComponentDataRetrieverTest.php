<?php

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Client as GuzzleClient;

use OpenComponents\ComponentDataRetriever;
use OpenComponents\Model\Component;

class ComponentDataRetrieverTest extends TestCase
{

    private $componentResponse = '{
        "type": "oc-component",
        "version": "0.36.15",
        "requestVersion": "",
        "name": "oc-client",
        "renderMode": "rendered",
        "href": "https://some-repo/oc-client",
        "html": "<script src=\"//s3-eu-west-1.amazonaws.com/somebucket/components/oc-client/0.36.15/src/oc-client.min.js\" type=\"text/javascript\"></script>"
    }';

    // If there's only one component it should request via GET,
    // else it should do a POST request
    public function testRequestMethodForOneComp()
    {
        $oneComp = [
            "registries" => [
                "serverRendering" => "https://some-registry.com"
            ],
            'components' => [
                'oc-client'
            ]
        ];
        $httpClient = $this->mockingRegistryCalls();

        $compDataRetriever = $this->getMockBuilder(ComponentDataRetriever::class)
            ->setConstructorArgs([$oneComp])
            ->setMethods(['performGet'])
            ->getMock();
        $compDataRetriever->expects($this->once())
            ->method('performGet');
        $compDataRetriever->setHttpClient($httpClient);

        $compDataRetriever->performRequest();

        // Testing with more than one component
        $twoComp = [
            "registries" => [
                "serverRendering" => "https://some-registry.com"
            ],
            'components' => [
                'oc-client',
                'some-other-component'
            ]
        ];
        $compDataRetrieverTwo = $this->getMockBuilder(ComponentDataRetriever::class)
            ->setConstructorArgs([$twoComp])
            ->setMethods(['performPost'])
            ->getMock();
        $compDataRetrieverTwo->expects($this->once())
            ->method('performPost');
        $compDataRetriever->setHttpClient($httpClient);

        $compDataRetrieverTwo->performRequest();
    }

    // Testing GET Request
    public function testPerformGet()
    {
        $config = [
            "registries" => [
                "serverRendering" => "https://some-registry.com"
            ],
            'components' => [
                'oc-client'
            ]
        ];

        $compDataRetriever = new ComponentDataRetriever($config);

        // Mocking http client
        $httpClient = $this->mockingRegistryCalls();
        $compDataRetriever->setHttpClient($httpClient);

        $response = $compDataRetriever->performGet(
            new Component($config['components'][0])
        );

        $this->assertEquals($response, $this->componentResponse);
    }

    // Testing POST Request
    public function testPerformPost()
    {
        $config = [
            "registries" => [
                "serverRendering" => "https://some-registry.com"
            ],
            'components' => [
                'oc-client',
                'some-other-component'
            ]
        ];

        $compDataRetriever = new ComponentDataRetriever($config);

        // Mocking http client
        $httpClient = $this->mockingRegistryCalls();
        $compDataRetriever->setHttpClient($httpClient);

        $response = $compDataRetriever->performPost($config['components']);

        $this->assertEquals($response, $this->componentResponse);
    }

    private function mockingRegistryCalls()
    {
        $mock = new MockHandler([
            new Response(200, [], $this->componentResponse),
            new Response(200, [], $this->componentResponse),
            new Response(200, [], $this->componentResponse)
        ]);

        $handler = HandlerStack::create($mock);
        return new GuzzleClient([
            'handler' => $handler
        ]);
    }
}
