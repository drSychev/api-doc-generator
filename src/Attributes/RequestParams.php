<?php

namespace Components\ApiDocGenerator\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class RequestParams
{
    public string $factory;

    public function __construct(string $requestParamsFactory)
    {
        $this->factory = $requestParamsFactory;
    }

}