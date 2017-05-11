<?php

namespace OpenComponents;

use OpenComponents\Model\Component;

class MappingUtils
{
    public function arrayToComponent($arrayComp)
    {
        $name = $arrayComp['name'];
        $version = isset($arrayComp['version']) ? $arrayComp['version']: '';
        $parameters = isset($arrayComp['parameters']) ? $arrayComp['parameters']: [];
        return new Component($name, $version, $parameters);
    }
}
