<?php

namespace Components\ApiDocGenerator\Information;

use Components\ApiDocGenerator\Attributes\RequestBody as RequestBodyAttribute;
use Components\ApiDocGenerator\Attributes\RequestParams as RequestParamsAttribute;
use Components\ApiDocGenerator\Attributes\Response as ResponseAttribute;
use Components\ApiDocGenerator\Factories\RequestBodyFactory;
use Components\ApiDocGenerator\Factories\RequestParamsFactory;
use Components\ApiDocGenerator\Factories\ResponseFactory;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\RequestBody;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionClass;
use ReflectionMethod;

class BaseInformation
{
    protected ReflectionClass $controllerRefClass;
    protected ReflectionMethod $methodRefClass;
    /**
     * @return Parameter[]
     */
    public function getRequestParams(): array
    {
        $requestBodyAttribute = $this->methodRefClass->getAttributes(RequestParamsAttribute::class)[0] ?? null;
        if ($requestBodyAttribute) {
            /** @var RequestParamsFactory $requestParams */
            $requestParams = new ($requestBodyAttribute->getArguments()[0]);
            return $requestParams->build();
        }
        return [];
    }

    protected function setReflectionInformation(string $controller, string $method)
    {
        $this->controllerRefClass = new \ReflectionClass($controller);
        $this->methodRefClass = $this->controllerRefClass->getMethod($method);
    }

    public function getRequestBody() :?RequestBody
    {
        $requestBodyAttribute = $this->methodRefClass->getAttributes(RequestBodyAttribute::class)[0] ?? null;
        if ($requestBodyAttribute) {
            /** @var RequestBodyFactory $requestBody */
            $requestBody = new ($requestBodyAttribute->getArguments()[0]);
            return $requestBody->build();
        }
        return null;
    }

    /**
     * @return Response[]
     */
    public function getResponses(): array
    {
        $responses = [];
        $responseAttributes = $this->methodRefClass->getAttributes(ResponseAttribute::class);
        foreach ($responseAttributes as $responseAttribute) {
            /** @var ResponseFactory $response */
            $response = new ($responseAttribute->getArguments()[0]);
            $responses[] = $response->build();
        }
        return $responses;
    }

    public function getDockBlock(): DocBlock
    {
        $factory = DocBlockFactory::createInstance();
        return $factory->create($this->methodRefClass->getDocComment() ?: ' ');
    }

}