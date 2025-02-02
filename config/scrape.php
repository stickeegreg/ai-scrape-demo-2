<?php

return [
    'anthropic' => [
        'api_key' => env('ANTHROPIC_API_KEY'),
    ],
    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
    ],
    'no_vnc_addresses' => array_filter(array_map('trim', explode(',', env('NO_VNC_ADDRESSES', '')))),
];
