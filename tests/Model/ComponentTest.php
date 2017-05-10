<?php

use PHPUnit\Framework\TestCase;

use OpenComponents\Model\Component;

class ComponentTest extends TestCase
{
    public function testConstructor()
    {
        $component = new Component('hello-world', '1.2.3', [], false, '');

        $this->assertInstanceOf(
            Component::class,
            $component
        );

        $this->assertEquals('1.2.3', $component->getVersion());
    }
}
