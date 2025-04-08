<?php

namespace App\Tools\JsonSchema;

use JsonSerializable;

interface JsonSchemaType extends JsonSerializable
{
    public function setDescription(string $description): void;
    public function toPhpValue(mixed $value): mixed;
}
