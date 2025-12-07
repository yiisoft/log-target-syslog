<?php

declare(strict_types=1);

use Psr\Log\LogLevel;

return [
    'yiisoft/log-target-syslog' => [
        'syslogTarget' => [
            'identity' => 'app',
            // Uncomment to filter by log levels:
            // 'levels' => [LogLevel::ERROR, LogLevel::WARNING],
        ],
    ],
];
