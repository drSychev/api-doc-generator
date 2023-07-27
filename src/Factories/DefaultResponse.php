<?php

namespace Components\ApiDocGenerator\Factories;

use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class DefaultResponse extends ResponseFactory
{

    public function build(): Response
    {
        return Response::create()->statusCode(200)->content(MediaType::json()->schema(Schema::object()));
    }
}