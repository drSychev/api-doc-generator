<?php

namespace Components\ApiDocGenerator\Factories;

use GoldSpecDigital\ObjectOrientedOAS\Objects\RequestBody;

abstract class RequestBodyFactory
{
    abstract public function build(): RequestBody;
}