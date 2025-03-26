<?php

namespace Tests\Fixtures;

use App\Tools\Attributes\ToolProperty;

class SampleObjectSimpleInt
{
    #[ToolProperty('desc')]
    public int $x;
}
