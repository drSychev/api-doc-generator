<?php

namespace Components\ApiDocGenerator\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
#[Attribute(Attribute::TARGET_CLASS)]
class Tag
{
    public string $tagFactory;

    public function __construct(string $tagFactory)
    {
        $this->tagFactory = $tagFactory;
    }
}