<?php

use PHPUnit\Framework\TestCase;

use OpenComponents\Client;

class ClientTest extends TestCase
{
    public function testConstructor()
    {
        $client = new Client();

        $this->assertInstanceOf(
            Client::class,
            $client
        );
    }
}
