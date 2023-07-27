<?php

namespace Components\ApiDocGenerator\Services;

use GoldSpecDigital\ObjectOrientedOAS\Contracts\SchemaContract;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\RequestBody;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class RequestBodyMergeService
{

    private ?RequestBody $mainBody = null;
    /**
     * @var SchemaContract[]
     */
    private array $properties = [];
    /**
     * @var string[]
     */
    private array $required = [];


    /**
     * @var MediaType[]
     */
    private array $mediaTypes = [];


    public static function create(): static
    {
        return new static();
    }


    public function add(?RequestBody $requestBody): void
    {
        if (!$requestBody) {
            return;
        }

        if (!$this->mainBody) {
            $this->mainBody = $requestBody;
        }

        foreach ($requestBody->content as $mediaType) {
            if ($mediaType->mediaType !== MediaType::MEDIA_TYPE_APPLICATION_JSON) {
                $mediaType[] = $mediaType;
                continue;
            }
            $this->properties = array_merge($this->properties, $mediaType->schema->properties ?? []);
            if ($mediaType->schema->required) {
                $this->required = array_merge($mediaType->schema->required, $this->required);
            }
        }
    }


    public function get(): ?RequestBody
    {
        if (!$this->mainBody) {
            return null;
        }

        if (!empty($this->properties)) {
            $this->mediaTypes[] = MediaType::json()
                ->schema(Schema::object()->properties(...$this->properties)->required(...$this->required));
        }
        return $this->mainBody->content(...$this->mediaTypes);
    }

}