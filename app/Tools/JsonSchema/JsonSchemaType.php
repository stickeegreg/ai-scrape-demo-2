<?php

namespace App\Tools\JsonSchema;

use JsonSerializable;

interface JsonSchemaType extends JsonSerializable
{
    public function setDescription(string $description): void;
}
