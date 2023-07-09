<?php

namespace Components\ApiDocGenerator\Factories;

use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;

abstract class RequestParamsFactory
{
    /**
     * @return Parameter[]
     */
   abstract public function build(): array;

}