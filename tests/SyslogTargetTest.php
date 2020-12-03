<?php

declare(strict_types=1);

namespace Yiisoft\Log\Target\Syslog {
    function openlog(...$args)
    {
        return \Yiisoft\Log\Target\Syslog\Tests\SyslogTargetTest::openlog($args);
    }

    function syslog(...$args)
    {
        return \Yiisoft\Log\Target\Syslog\Tests\SyslogTargetTest::syslog($args);
    }

    function closelog(...$args)
    {
        return \Yiisoft\Log\Target\Syslog\Tests\SyslogTargetTest::closelog($args);
    }
}

namespace Yiisoft\Log\Target\Syslog\Tests {
    use Psr\Log\LogLevel;
    use RuntimeException;
    use Yiisoft\Log\Message;
    use Yiisoft\Log\Target\Syslog\SyslogTarget;

    final class SyslogTargetTest extends \PHPUnit\Framework\TestCase
    {
        /**
         * Array of static functions.
         *
         * @var array
         */
        public static $functions = [];

        /**
         * @covers \Yiisoft\Log\Target\Syslog\SyslogTarget::export()
         */
        public function testExport(): void
        {
            $identity = 'identity string';
            $options = LOG_ODELAY | LOG_PID;
            $facility = LOG_USER;

            $messages = [
                new Message(LogLevel::INFO, 'info message', ['category' => 'app']),
                new Message(LogLevel::ERROR, 'error message', ['category' => 'app']),
                new Message(LogLevel::WARNING, 'warning message', ['category' => 'app']),
                new Message(LogLevel::DEBUG, 'trace message', ['category' => 'app']),
                new Message(LogLevel::NOTICE, 'notice message', ['category' => 'app']),
                new Message(LogLevel::EMERGENCY, 'emergency message', ['category' => 'app']),
                new Message(LogLevel::ALERT, 'alert message', ['category' => 'app']),
            ];

            /* @var $syslogTarget SyslogTarget */
            $syslogTarget = $this->getMockBuilder(SyslogTarget::class)
                ->setMethods(['openlog', 'syslog', 'formatMessages', 'closelog'])
                ->getMock();

            $syslogTarget->setFormat(static function (Message $message) {
                return "[{$message->level()}][{$message->context('category', '')}] {$message->message()}";
            });

            $syslogTarget
                ->setIdentity($identity)
                ->setOptions($options)
                ->setFacility($facility)
                ->collect($messages, false)
            ;

            $syslogTarget->expects($this->once())
                ->method('openlog')
                ->with(
                    $this->equalTo($identity),
                    $this->equalTo($options),
                    $this->equalTo($facility)
                );

            $syslogTarget->expects($this->exactly(7))
                ->method('syslog')
                ->withConsecutive(
                    [$this->equalTo(LOG_INFO), $this->equalTo('[info][app] info message')],
                    [$this->equalTo(LOG_ERR), $this->equalTo('[error][app] error message')],
                    [$this->equalTo(LOG_WARNING), $this->equalTo('[warning][app] warning message')],
                    [$this->equalTo(LOG_DEBUG), $this->equalTo('[debug][app] trace message')],
                    [$this->equalTo(LOG_NOTICE), $this->equalTo('[notice][app] notice message')],
                    [$this->equalTo(LOG_EMERG), $this->equalTo('[emergency][app] emergency message')],
                    [$this->equalTo(LOG_ALERT), $this->equalTo('[alert][app] alert message')],
                );

            $syslogTarget->expects($this->once())->method('closelog');

            self::$functions['openlog'] = function ($arguments) use ($syslogTarget) {
                $this->assertCount(3, $arguments);
                [$identity, $option, $facility] = $arguments;
                return $syslogTarget->openlog($identity, $option, $facility);
            };

            self::$functions['syslog'] = function ($arguments) use ($syslogTarget) {
                $this->assertCount(2, $arguments);
                [$priority, $message] = $arguments;
                return $syslogTarget->syslog($priority, $message);
            };

            self::$functions['closelog'] = function ($arguments) use ($syslogTarget) {
                $this->assertCount(0, $arguments);
                return $syslogTarget->closelog();
            };

            $syslogTarget->export();
        }

        /**
         * @covers \Yiisoft\Log\Target\Syslog\SyslogTarget::export()
         *
         * See https://github.com/yiisoft/yii2/issues/14296
         */
        public function testFailedExport(): void
        {
            /** @var SyslogTarget $syslogTarget */
            $syslogTarget = $this->getMockBuilder(SyslogTarget::class)
                ->setMethods(['openlog', 'syslog', 'formatMessages', 'closelog'])
                ->getMock();

            $syslogTarget
                ->setIdentity('identity string')
                ->setFacility(LOG_USER)
                ->setOptions(LOG_ODELAY | LOG_PID);

            $syslogTarget->method('syslog')->willReturn(false);
            $syslogTarget->collect([new Message(LogLevel::INFO, 'test', ['category' => 'app'])], false);

            self::$functions['openlog'] = function ($arguments) use ($syslogTarget) {
                $this->assertCount(3, $arguments);
                [$identity, $option, $facility] = $arguments;
                return $syslogTarget->openlog($identity, $option, $facility);
            };
            self::$functions['syslog'] = function ($arguments) use ($syslogTarget) {
                $this->assertCount(2, $arguments);
                [$priority, $message] = $arguments;
                return $syslogTarget->syslog($priority, $message);
            };
            self::$functions['closelog'] = function ($arguments) use ($syslogTarget) {
                $this->assertCount(0, $arguments);
                return $syslogTarget->closelog();
            };

            $this->expectException(RuntimeException::class);
            $syslogTarget->export();
        }

        /**
         * @param $name
         * @param $arguments
         *
         * @return mixed
         */
        public static function __callStatic($name, $arguments)
        {
            if (isset(self::$functions[$name]) && is_callable(self::$functions[$name])) {
                $arguments = $arguments[0] ?? $arguments;
                return forward_static_call(self::$functions[$name], $arguments);
            }
            self::fail("Function '$name' has not implemented yet!");
        }
    }
}
