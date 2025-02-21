<?php


$noVncAddresses = array_filter(array_map('trim', explode(',', env('NO_VNC_ADDRESSES', ''))));
$controlServiceAddresses = array_filter(array_map('trim', explode(',', env('CONTROL_SERVICE_ADDRESSES', ''))));
$servers = [];

foreach ($noVncAddresses as $i => $_) {
    $servers[] = [
        'vnc' => $noVncAddresses[$i],
        'control' => $controlServiceAddresses[$i],
    ];
}

return [
    'anthropic' => [
        'api_key' => env('ANTHROPIC_API_KEY'),
    ],
    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
    ],
    'servers' => $servers,
];
