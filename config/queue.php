<?php
return [
    // 默认使用的队列驱动
    'default' => env('QUEUE_DRIVER', 'sync'),

    // 队列连接信息
    'connections' => [
        'sync' => [
            'driver' => 'sync',
        ],
        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
            'queue' => 'default',
            'expire' => 60,
        ]
    ],
];