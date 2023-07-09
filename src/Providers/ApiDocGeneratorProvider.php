<?php

namespace Components\ApiDocGenerator\Providers;

use Components\ApiDocGenerator\Commands\MakeSpecificationsCommand;
use Illuminate\Support\ServiceProvider;

class ApiDocGeneratorProvider extends ServiceProvider
{
    public function register()
    {
        $this->commands([
            MakeSpecificationsCommand::class
        ]);
        $this->mergeConfigFrom(dirname(__DIR__, 2) . '/config/api_doc_generator.php', 'api_doc_generator');
    }

    public function boot()
    {
        $this->loadViewsFrom(dirname(__DIR__, 2) . '/resources/views', 'api_doc_generator');
        $this->loadRoutesFrom(dirname(__DIR__, 2) . '/routes/api.php');
    }

}