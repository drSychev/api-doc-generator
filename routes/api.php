<?php

use Components\ApiDocGenerator\Controllers\SpecificationController;
use Components\ApiDocGenerator\Middlewares\AccessInProdMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware(AccessInProdMiddleware::class)->group(function () {
    Route::get(config('api_doc_generator.router'), [SpecificationController::class, 'index']);
    Route::get('_api-doc-generator/schema/{schema}', [SpecificationController::class, 'schema'])
        ->name('api-doc-generator-schema');
});