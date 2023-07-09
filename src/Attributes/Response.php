<?php

namespace Components\ApiDocGenerator\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
#[Attribute(Attribute::IS_REPEATABLE)]
class Response
{
    public string $factory;

    public function __construct(string $responseFactory)
    {
        $this->factory = $responseFactory;
    }
}