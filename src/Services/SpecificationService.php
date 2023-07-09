<?php


namespace Components\ApiDocGenerator\Services;


use Components\ApiDocGenerator\Information\Middleware;
use Components\ApiDocGenerator\Information\Path;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Info;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Server;
use GoldSpecDigital\ObjectOrientedOAS\OpenApi;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SpecificationService
{
    private Router $router;
    private array $paths = [];
    private array $middlewares = [];
    private array $tags = [];

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    private string $dir = 'public/swagger';
    private string $filePrefix = '.json';

    /**
     * @return string
     */
    public function getDir(): string
    {
        return $this->dir;
    }

    /**
     * @return string
     */
    public function getFilePrefix(): string
    {
        return $this->filePrefix;
    }

    private function getConfig(string $key, string $schema, mixed $default = null)
    {
        return config("api_doc_generator.schema_settings.$schema.$key")
            ?: config("api_doc_generator.schema_settings.default.$key")
                ?: $default;
    }

    public function makeApi(string $schema, string $protocol, string $host, string $basePath, array $paths, array $tags): void
    {
        $url = $this->getConfig('base_url', $schema, "$protocol://$host/$basePath");
        $server = Server::create()->url($url);
        $info = Info::create()
            ->title($this->getConfig('title', $schema, config('app.name') . ' API Specification'))
            ->version($this->getConfig('version', $schema, 'v1'))
            ->description($this->getConfig('description', $schema));
        $openApi = OpenApi::create()
            ->servers($server)
            ->tags(...$tags)
            ->openapi(OpenApi::OPENAPI_3_0_2)
            ->paths(...$paths)
            ->info($info);

        $basePath = trim(config('api_doc_generator.path'), '/');
        Storage::disk(config('api_doc_generator.file_storage_disk'))
            ->put("$basePath/{$schema}.json", $openApi->toJson());
    }

    public function makeSpecification(): void
    {
        $basePath = 'api';
        $host = request()->getHost();
        $protocol = request()->secure() ? 'https' : 'http';

        foreach ($this->router->getRoutes()->getRoutes() as $route) {
            $pathInf = Path::create($route, $basePath);

            if (!Str::startsWith($pathInf->route->uri, 'api') || !$pathInf->isValid()) {
                continue;
            }
            $schema = $pathInf->getSchema();
            if (!$schema) {
                continue;
            }

            $requestBodyMergeService = RequestBodyMergeService::create();
            $requestBodyMergeService->add($pathInf->getRequestBody());
            $requestParams = $pathInf->getRequestParams();
            $responses = $pathInf->getResponses();
            $uri = $pathInf->getUri();
            $comment = $pathInf->getDockBlock()->getSummary();

            $operations = [];
            foreach ($route->methods as $method) {
                if ($method === 'HEAD') {
                    continue;
                }
                $middlewares = $this->router->gatherRouteMiddleware($route);
                foreach ($middlewares as $middleware) {
                    $middlewareInf = $this->getMiddlewareInformation($middleware);
                    $requestParams = array_merge($requestParams, $middlewareInf->getRequestParams());
                    $responses = array_merge($responses, $middlewareInf->getResponses());
                    $requestBodyMergeService->add($middlewareInf->getRequestBody());
                }

                $operation = Operation::create()->action(Str::lower($method))
                    ->parameters(...$requestParams)
                    ->requestBody($requestBodyMergeService->get())
                    ->responses(...$responses)
                    ->description($pathInf->getController())
                    ->summary($comment);

                $tag = $pathInf->getTag();
                if ($tag) {
                    $this->tags[$schema][] = $tag;
                    $operation = $operation->tags($tag);
                }
                $operations[] = $operation;
            }

            $this->paths[$schema][] = PathItem::create()->route($uri)->operations(...$operations);
        }

        foreach ($this->paths as $schema => $paths) {
            $this->makeApi($schema, $protocol, $host, $basePath, $paths, $this->tags[$schema]);
        }
    }

    public function getMiddlewareInformation(string $className): Middleware
    {
        if (!isset($this->middlewares[$className])) {
            $this->middlewares[$className] = Middleware::create($className);
        }
        return $this->middlewares[$className];
    }

    public function validate()
    {
    }
}
