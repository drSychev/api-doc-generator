<?php

namespace Components\ApiDocGenerator\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
#[Attribute(Attribute::IS_REPEATABLE)]
class RequestBody
{
    public string $factory;

    public function __construct(string $requestBodyFactory)
    {
        $this->factory = $requestBodyFactory;
    }
}