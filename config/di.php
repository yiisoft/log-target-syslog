<?php

declare(strict_types=1);

use Yiisoft\Log\Target\Syslog\SyslogTarget;

/* @var $params array */

return [
    SyslogTarget::class => static fn () => new SyslogTarget(
        $params['yiisoft/log-target-syslog']['syslogTarget']['identity'],
    ),
];
