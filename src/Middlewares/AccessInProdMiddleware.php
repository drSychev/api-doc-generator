<?php

namespace Components\ApiDocGenerator\Middlewares;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AccessInProdMiddleware
{
    public function handle($request, $next)
    {
        if (app()->environment('production') || config('api_doc_generator.access_in_prod')) {
            throw new NotFoundHttpException();
        }
        return $next($request);
    }

}