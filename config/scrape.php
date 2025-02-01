<?php

return [
    'anthropic' => [
        'api_key' => env('ANTHROPIC_API_KEY'),
    ],
    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
    ],
    'vnc_addresses' => array_filter(array_map('trim', explode(',', env('VNC_ADDRESSES')))),
];
