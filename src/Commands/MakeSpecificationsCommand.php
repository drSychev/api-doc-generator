<?php

namespace Components\ApiDocGenerator\Commands;

use Components\ApiDocGenerator\Services\SpecificationService;
use Illuminate\Console\Command;

class MakeSpecificationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api-doc-generator:make-specifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Сгенерировать спецификации';


    public function handle(SpecificationService $service): int
    {
        $service->makeSpecification();
        return 0;
    }
}