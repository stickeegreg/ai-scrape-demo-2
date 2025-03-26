<?php

namespace App\Tools\JsonSchema;

use App\Tools\JsonSchema\JsonSchemaType;

abstract class AbstractJsonSchemaType implements JsonSchemaType
{
    protected string $description = '';

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
}
