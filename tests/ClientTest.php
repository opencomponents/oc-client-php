<?php

use PHPUnit\Framework\TestCase;

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
