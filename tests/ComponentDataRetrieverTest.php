<?php

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Middleware;

use OpenComponents\ComponentDataRetriever;
use OpenComponents\Model\Component;

class ComponentDataRetrieverTest extends TestCase
{
    private $config = [
        "registries" => [
            "serverRendering" => "https://some-registry.com"
        ],
        'components' => [
            'oc-client'
        ]
    ];

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
        $httpClient = $this->mockingRegistryCalls();

        $compDataRetriever = $this->getMockBuilder(ComponentDataRetriever::class)
            ->setConstructorArgs([$this->config])
            ->setMethods(['performGet'])
            ->getMock();
        $compDataRetriever->expects($this->once())
            ->method('performGet');
        $compDataRetriever->setHttpClient($httpClient);

        $compDataRetriever->performRequest();

        // Testing with more than one component
        $twoComp = $this->config;
        $twoComp['components'][] = 'some-other-component';
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
        $compDataRetriever = new ComponentDataRetriever($this->config);

        // Mocking http client
        $httpClient = $this->mockingRegistryCalls();
        $compDataRetriever->setHttpClient($httpClient);

        $response = $compDataRetriever->performGet(
            new Component($this->config['components'][0])
        );

        $this->assertEquals($response, $this->componentResponse);
    }

    public function testPerformGetWithParameters()
    {
        $compDataRetriever = new ComponentDataRetriever($this->config);

        $container = [];
        $client = $this->mockingRegistryCalls($container);
        $compDataRetriever->setHttpClient($client);

        $compDataRetriever->performGet(
            new Component(
                $this->config['components'][0],
                '',
                [
                    'test' => 'param',
                    'deeper' => [
                        'level' => true
                    ]
                ]
            )
        );

        $this->assertEquals(1, count($container));
        $request = $container[0]['request'];
        $this->assertEquals('oc-client', $request->getUri()->getPath());
        $this->assertEquals('test=param&deeper%5Blevel%5D=1', $request->getUri()->getQuery());
    }

    public function testPerformGetWithVersion()
    {
        $compDataRetriever = new ComponentDataRetriever($this->config);

        $container = [];
        $client = $this->mockingRegistryCalls($container);
        $compDataRetriever->setHttpClient($client);

        $compDataRetriever->performGet(
            new Component(
                $this->config['components'][0],
                '0.30',
                [
                    'test' => 'param',
                    'deeper' => [
                        'level' => true
                    ]
                ]
            )
        );

        $this->assertEquals(1, count($container));
        $request = $container[0]['request'];
        $this->assertEquals('oc-client/0.30', $request->getUri()->getPath());
    }

    // Testing POST Request
    public function testPerformPost()
    {
        $twoComp = $this->config;
        $twoComp['components'][] = 'some-other-component';
        $compDataRetriever = new ComponentDataRetriever($twoComp);

        // Mocking http client
        $httpClient = $this->mockingRegistryCalls();
        $compDataRetriever->setHttpClient($httpClient);

        $response = $compDataRetriever->performPost($twoComp['components']);

        $this->assertEquals($response, $this->componentResponse);
    }

    public function testPerformPostWithParameters()
    {
        $compDataRetriever = new ComponentDataRetriever($this->config);

        $container = [];
        $client = $this->mockingRegistryCalls($container);
        $compDataRetriever->setHttpClient($client);

        $components = [
            [
                'name' => 'oc-client',
                'parameters' => [
                    'some' => 'param',
                    'some-other' => 'param'
                ]
            ],
            [
                'name' => 'other-amazing-component'
            ]
        ];

        $compDataRetriever->performPost($components);
        $this->assertEquals(1, count($container));
        $request = $container[0]['request'];
        $this->assertEquals('{"components":[{"name":"oc-client","parameters":{"some":"param","some-other":"param"}},{"name":"other-amazing-component"}]}', (string) $request->getBody());
    }

    public function testPostWithVersion()
    {
        $compDataRetriever = new ComponentDataRetriever($this->config);

        $container = [];
        $client = $this->mockingRegistryCalls($container);
        $compDataRetriever->setHttpClient($client);

        $components = [
            [
                'name' => 'oc-client',
                'version' => '0.30',
                'parameters' => [
                    'some' => 'param',
                    'some-other' => 'param'
                ]
            ],
            [
                'name' => 'other-amazing-component'
            ]
        ];
        $compDataRetriever->performPost($components);
        $this->assertEquals(1, count($container));
        $request = $container[0]['request'];
        $this->assertEquals('{"components":[{"name":"oc-client","version":"0.30","parameters":{"some":"param","some-other":"param"}},{"name":"other-amazing-component"}]}', (string) $request->getBody());
    }

    private function mockingRegistryCalls(&$container = [])
    {
        $history = Middleware::history($container);

        $stack = HandlerStack::create($this->getMockHandler());

        $stack->push($history);

        $client = new GuzzleClient([
            'handler' => $stack
        ]);

        return $client;
    }

    private function getMockHandler()
    {
        $mock = new MockHandler([
            new Response(200, [], $this->componentResponse),
            new Response(200, [], $this->componentResponse),
            new Response(200, [], $this->componentResponse)
        ]);

        return $mock;
    }
}
