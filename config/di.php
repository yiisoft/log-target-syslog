<?php

declare(strict_types=1);

use Yiisoft\Log\Target\Syslog\SyslogTarget;

/** @var array $params */

return [
    SyslogTarget::class => static fn() => new SyslogTarget(
        identity: $params['yiisoft/log-target-syslog']['syslogTarget']['identity'],
        levels: $params['yiisoft/log-target-syslog']['syslogTarget']['levels'] ?? [],
    ),
];
