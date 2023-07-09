<?php


namespace Components\ApiDocGenerator\Information;


use \Components\ApiDocGenerator\Attributes\Tag as TagAttribute;
use Components\ApiDocGenerator\Factories\TagFactory;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Tag;
use Illuminate\Routing\Route;
use Illuminate\Support\Str;

class Path extends BaseInformation
{
    public Route $route;
    private string $basePath;
    private string $controller;
    private string $method;
    private bool $isValid = true;

    public function __construct(Route $route, string $baseBath)
    {
        $this->route = $route;
        $this->basePath = $baseBath;
        if ($route->getActionName() === 'Closure' || Str::startsWith($route->uri, '_')) {
            $this->isClosure = false;
            return;
        }
        [$this->controller, $this->method] = explode('@', $this->route->getActionName());
        $this->setReflectionInformation($this->controller, $this->method);
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public static function create(Route $route, string $basePath): static
    {
        return new static($route, $basePath);
    }

    public function getSchema(): ?string
    {
        return explode('/', $this->route->getPrefix())[1] ?? null;
    }

    public function getGroup(): ?string
    {
        $uriPrefix = $this->route->getPrefix();
        if ($uriPrefix) {
            $chunks = explode('/', $uriPrefix);
            if (!empty($chunks)) {
                return $chunks[count($chunks) - 1];
            }
        }
        return null;
    }

    public function getUri(): string
    {
        return Str::replaceFirst($this->basePath, '', $this->route->uri);
    }

    public function getController(): string
    {
        return $this->controller;
    }

    public function getTag(): ?Tag
    {
        $tagAttribute = $this->methodRefClass->getAttributes(TagAttribute::class)[0] ?? null;
        if (is_null($tagAttribute)) {
            $tagAttribute = $this->controllerRefClass->getAttributes(TagAttribute::class)[0] ?? null;
            if (is_null($tagAttribute)) {
                $group = $this->getGroup();
                if ($group) {
                    return Tag::create()->name(Str::ucfirst($group));
                }
            }
        }
        /** @var TagFactory $tag */
        $tag = new($tagAttribute->getArguments()[0]);
        return $tag->build();
    }

}
