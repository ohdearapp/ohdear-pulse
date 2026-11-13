<?php

return [
    'oh_dear' => [
        'pulse' => [
            'api_key' => env('OH_DEAR_API_TOKEN'),
            'monitor_id' => env('OH_DEAR_MONITOR_ID'),
            'broken_links_monitor_id' => env('OH_DEAR_BROKEN_LINKS_MONITOR_ID', env('OH_DEAR_MONITOR_ID')),
        ],
    ],
];
