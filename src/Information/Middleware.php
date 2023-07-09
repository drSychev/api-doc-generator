<?php

namespace Components\ApiDocGenerator\Information;

use Illuminate\Routing\Router;

class Middleware extends BaseInformation
{
    public function __construct(private Router $router, private string $name)
    {
            $this->init($this->router, $this->name);
    }

    private function init(Router $router, string $name)
    {

        $class = $this->isClass() ? $name : $router->getMiddleware()[$name];
        $this->setReflectionInformation($class, 'handle');
    }

    public static function create(string $name): static
    {
        $router = app()->get(Router::class);
        return new static($router, $name);
    }

    public function isClass(): bool
    {
        return class_exists($this->name);
    }


}