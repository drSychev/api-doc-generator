<?php

namespace Components\ApiDocGenerator\Controllers;

use Components\ApiDocGenerator\Services\SpecificationService;
use Components\LaravelApi\Components\BaseApi;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SpecificationController
{
    public function index(SpecificationService $service): Factory|View
    {
        if (config('api_doc_generator.make_after_reload_page')) {
            $service->makeSpecification();
        }
        /**
         * @var BaseApi $api
         */
        $filesData = [];
        $folder = trim(config('api_doc_generator.path'), '/');
        $storage = Storage::disk(config('api_doc_generator.file_storage_disk'));

        $versions = $storage->files($folder);
        foreach ($versions as $version) {
            $chunks = explode('.', $version);
            if (count($chunks) < 2 || $chunks[1] !== 'json') {
                continue;
            }
            $name = preg_replace('/^.+\//', '', $chunks[0]);
            $timestamp = $storage->lastModified($version);
            $url =  route('api-doc-generator-schema', ['schema' => "$name.json"]) . '?r=' . $timestamp;
            $filesData[] = ['url' => $url, 'name' => $name];
        }

        if (!$filesData) {
            $filesData[] = ['url' => '/', 'name' => 'default'];
        }
        return view('api_doc_generator::api/documentation', [
            'filesJsonData' => json_encode($filesData)
        ]);
    }

    public function getSwaggerTheme(string $fileName): string|Response
    {
        $path = dirname(__DIR__) . '/../resources/swagger-themes/';

        if (!file_exists($path . $fileName)) {
            return '';
        }
        $file = file_get_contents($path . $fileName);

        return new Response(
            $file,
            200,
            [
                'Content-Type' => 'text/css',
            ]
        );

    }

    public function schema(string $schema): StreamedResponse
    {
        $folder = trim(config('api_doc_generator.path'), '/');
        return Storage::disk(config('api_doc_generator.file_storage_disk'))
            ->response("$folder/$schema");
    }

}