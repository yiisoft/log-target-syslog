<?php

declare(strict_types=1);

use Psr\Log\LoggerInterface;
use Yiisoft\Log\Logger;
use Yiisoft\Log\Target\Syslog\SyslogTarget;

/* @var $params array */

return [
    LoggerInterface::class => static fn (SyslogTarget $syslogTarget) => new Logger([$syslogTarget]),

    SyslogTarget::class => static fn () => new SyslogTarget(
        $params['yiisoft/log-target-syslog']['syslogTarget']['identity'],
    ),
];
