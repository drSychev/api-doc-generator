<?php

namespace Components\ApiDocGenerator\Factories;

use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;

abstract class ResponseFactory
{
    abstract public function build(): Response;
}