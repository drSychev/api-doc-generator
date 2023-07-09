<?php

namespace Components\ApiDocGenerator\Factories;

use GoldSpecDigital\ObjectOrientedOAS\Objects\Tag;;

abstract class TagFactory
{
    abstract public function build(): Tag;
}