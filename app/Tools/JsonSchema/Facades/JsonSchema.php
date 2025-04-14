<?php

namespace App\Tools\JsonSchema\Facades;

use Illuminate\Support\Facades\Facade;

class JsonSchema extends Facade
{
    /**
     * Get the facade accessor
     */
    #[\Override]
    protected static function getFacadeAccessor(): string
    {
        return \App\Tools\JsonSchema\JsonSchema::class;
    }
}
