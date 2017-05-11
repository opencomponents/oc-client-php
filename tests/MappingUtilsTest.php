<?php

use PHPUnit\Framework\TestCase;

use OpenComponents\MappingUtils;

class MappingUtilsTest extends TestCase
{
    public function testArrayToComponent()
    {
        $mappingUtils = new MappingUtils();

        $arrayComp = [
            'name' => 'oc-client'
        ];

        $component = $mappingUtils->arrayToComponent($arrayComp);
        $this->assertEquals('oc-client', $component->getName());

        $arrayComp = [
            'name' => 'other-comp-with-version',
            'version' => '1.2.3',
            'parameters' => [
                'one' => '',
                'two' => '',
                'three' => [
                    'nested' => 'one'
                ]
            ]
        ];
        $component = $mappingUtils->arrayToComponent($arrayComp);
        $this->assertEquals('other-comp-with-version', $component->getName());
        $this->assertEquals('1.2.3', $component->getVersion());
        $this->assertEquals(3, count($component->getParameters()));
    }
}
