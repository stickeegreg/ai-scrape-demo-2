<?php

namespace App\ScrapeStrategies;

enum ScrapeStrategy: string
{
    case AnthropicComputerUse = 'AnthropicComputerUse';
    case OpenAIO1WithAnthropicComputerUse = 'OpenAIO1WithAnthropicComputerUse';
    case OpenAIGPT4oMiniWithAnthropicComputerUse = 'OpenAIGPT4oMiniWithAnthropicComputerUse';
}
