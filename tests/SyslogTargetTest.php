<?php

declare(strict_types=1);

namespace Yiisoft\Log\Target\Syslog\Tests;

use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use RuntimeException;
use Yiisoft\Log\Message;
use Yiisoft\Log\Target\Syslog\SyslogTarget;

use const LOG_ALERT;
use const LOG_CRIT;
use const LOG_DEBUG;
use const LOG_EMERG;
use const LOG_ERR;
use const LOG_INFO;
use const LOG_NOTICE;
use const LOG_ODELAY;
use const LOG_PID;
use const LOG_USER;
use const LOG_WARNING;

final class SyslogTargetTest extends TestCase
{
    use PHPMock;

    public function testExport(): void
    {
        $identity = 'identity-string';
        $options = LOG_ODELAY | LOG_PID;
        $facility = LOG_USER;

        $messages = [
            new Message(LogLevel::INFO, 'info message', ['category' => 'app']),
            new Message(LogLevel::ERROR, 'error message', ['category' => 'app']),
            new Message(LogLevel::CRITICAL, 'critical message', ['category' => 'app']),
            new Message(LogLevel::WARNING, 'warning message', ['category' => 'app']),
            new Message(LogLevel::DEBUG, 'trace message', ['category' => 'app']),
            new Message(LogLevel::NOTICE, 'notice message', ['category' => 'app']),
            new Message(LogLevel::EMERGENCY, 'emergency message', ['category' => 'app']),
            new Message(LogLevel::ALERT, 'alert message', ['category' => 'app']),
        ];

        $syslogTarget = new SyslogTarget($identity, $options, $facility);

        $this
            ->getFunctionMock('Yiisoft\Log\Target\Syslog', 'openlog')
            ->expects($this->once())
            ->with(
                $this->equalTo($identity),
                $this->equalTo($options),
                $this->equalTo($facility),
            )
        ;

        $this
            ->getFunctionMock('Yiisoft\Log\Target\Syslog', 'syslog')
            ->expects($this->exactly(8))
            ->withConsecutive(
                [$this->equalTo(LOG_INFO), $this->equalTo('[info][app] info message')],
                [$this->equalTo(LOG_ERR), $this->equalTo('[error][app] error message')],
                [$this->equalTo(LOG_CRIT), $this->equalTo('[critical][app] critical message')],
                [$this->equalTo(LOG_WARNING), $this->equalTo('[warning][app] warning message')],
                [$this->equalTo(LOG_DEBUG), $this->equalTo('[debug][app] trace message')],
                [$this->equalTo(LOG_NOTICE), $this->equalTo('[notice][app] notice message')],
                [$this->equalTo(LOG_EMERG), $this->equalTo('[emergency][app] emergency message')],
                [$this->equalTo(LOG_ALERT), $this->equalTo('[alert][app] alert message')],
            )
        ;

        $this
            ->getFunctionMock('Yiisoft\Log\Target\Syslog', 'closelog')
            ->expects($this->once())
        ;

        $syslogTarget->collect($messages, true);
    }

    public function testSetFormatAndSetPrefixAndExport(): void
    {
        $syslogTarget = new SyslogTarget('identity-string');
        $syslogTarget->setPrefix(static fn () => 'Prefix ');
        $syslogTarget->setFormat(static fn (Message $message) => "[{$message->level()}] {$message->message()}");

        $this
            ->getFunctionMock('Yiisoft\Log\Target\Syslog', 'syslog')
            ->expects($this->once())
            ->with(
                $this->equalTo(LOG_INFO),
                $this->equalTo('Prefix [info] test')
            )
        ;

        $syslogTarget->collect([new Message(LogLevel::INFO, 'test', ['category' => 'app'])], true);
    }
}
