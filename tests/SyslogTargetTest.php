<?php
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

    use PHPUnit_Framework_MockObject_MockObject;
    use Psr\Log\LogLevel;
    use Yiisoft\Log\LogRuntimeException;
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
         * @var PHPUnit_Framework_MockObject_MockObject
         */
        protected $syslogTarget;

        /**
         * Set up syslogTarget as the mock object.
         */
        protected function setUp()
        {
            $this->syslogTarget = $this->getMockBuilder(SyslogTarget::class)
                ->setMethods(['getMessagePrefix'])
                ->getMock();
        }

        /**
         * @covers \Yiisoft\Log\Target\Syslog\SyslogTarget::export()
         */
        public function testExport(): void
        {
            $identity = 'identity string';
            $options = LOG_ODELAY | LOG_PID;
            $facility = LOG_USER;

            $messages = [
                [LogLevel::INFO, 'info message'],
                [LogLevel::ERROR, 'error message'],
                [LogLevel::WARNING, 'warning message'],
                [LogLevel::DEBUG, 'trace message'],
                [LogLevel::NOTICE, 'notice message'],
                [LogLevel::EMERGENCY, 'emergency message'],
                [LogLevel::ALERT, 'alert message'],
            ];

            /* @var $syslogTarget SyslogTarget|PHPUnit_Framework_MockObject_MockObject */
            $syslogTarget = $this->getMockBuilder(SyslogTarget::class)
                ->setMethods(['openlog', 'syslog', 'formatMessage', 'closelog'])
                ->getMock();

            $syslogTarget = $syslogTarget
                ->withIdentity($identity)
                ->withOptions($options)
                ->withFacility($facility)
                ->setMessages($messages);

            $syslogTarget->expects($this->once())
                ->method('openlog')
                ->with(
                    $this->equalTo($identity),
                    $this->equalTo($options),
                    $this->equalTo($facility)
                );

            $syslogTarget->expects($this->exactly(7))
                ->method('formatMessage')
                ->withConsecutive(
                    [$this->equalTo($messages[0])],
                    [$this->equalTo($messages[1])],
                    [$this->equalTo($messages[2])],
                    [$this->equalTo($messages[3])],
                    [$this->equalTo($messages[4])],
                    [$this->equalTo($messages[5])],
                    [$this->equalTo($messages[6])]
                )->willReturnMap([
                    [$messages[0], 'formatted message 1'],
                    [$messages[1], 'formatted message 2'],
                    [$messages[2], 'formatted message 3'],
                    [$messages[3], 'formatted message 4'],
                    [$messages[4], 'formatted message 5'],
                    [$messages[5], 'formatted message 6'],
                    [$messages[6], 'formatted message 7'],
                ]);

            $syslogTarget->expects($this->exactly(7))
                ->method('syslog')
                ->withConsecutive(
                    [$this->equalTo(LOG_INFO), $this->equalTo('formatted message 1')],
                    [$this->equalTo(LOG_ERR), $this->equalTo('formatted message 2')],
                    [$this->equalTo(LOG_WARNING), $this->equalTo('formatted message 3')],
                    [$this->equalTo(LOG_DEBUG), $this->equalTo('formatted message 4')],
                    [$this->equalTo(LOG_NOTICE), $this->equalTo('formatted message 5')],
                    [$this->equalTo(LOG_EMERG), $this->equalTo('formatted message 6')],
                    [$this->equalTo(LOG_ALERT), $this->equalTo('formatted message 7')]
                );

            $syslogTarget->expects($this->once())->method('closelog');

            static::$functions['openlog'] = function ($arguments) use ($syslogTarget) {
                $this->assertCount(3, $arguments);
                [$identity, $option, $facility] = $arguments;
                return $syslogTarget->openlog($identity, $option, $facility);
            };

            static::$functions['syslog'] = function ($arguments) use ($syslogTarget) {
                $this->assertCount(2, $arguments);
                [$priority, $message] = $arguments;
                return $syslogTarget->syslog($priority, $message);
            };

            static::$functions['closelog'] = function ($arguments) use ($syslogTarget) {
                $this->assertCount(0, $arguments);
                return $syslogTarget->closelog();
            };

            $syslogTarget->export();
        }

        /**
         * @covers Yiisoft\Log\Target\Syslog\SyslogTarget::export()
         *
         * See https://github.com/yiisoft/yii2/issues/14296
         */
        public function testFailedExport(): void
        {
            /** @var SyslogTarget $syslogTarget */
            $syslogTarget = $this->getMockBuilder(SyslogTarget::class)
                ->setMethods(['openlog', 'syslog', 'formatMessage', 'closelog'])
                ->getMock();

            $syslogTarget = $syslogTarget
                ->withIdentity('identity string')
                ->withFacility(LOG_USER)
                ->withOptions(LOG_ODELAY | LOG_PID);

            $syslogTarget->method('syslog')->willReturn(false);

            $syslogTarget = $syslogTarget->setMessages([
                [LogLevel::INFO, 'test', []],
            ]);

            static::$functions['openlog'] = function ($arguments) use ($syslogTarget) {
                $this->assertCount(3, $arguments);
                [$identity, $option, $facility] = $arguments;
                return $syslogTarget->openlog($identity, $option, $facility);
            };
            static::$functions['syslog'] = function ($arguments) use ($syslogTarget) {
                $this->assertCount(2, $arguments);
                [$priority, $message] = $arguments;
                return $syslogTarget->syslog($priority, $message);
            };
            static::$functions['closelog'] = function ($arguments) use ($syslogTarget) {
                $this->assertCount(0, $arguments);
                return $syslogTarget->closelog();
            };

            $this->expectException(LogRuntimeException::class);
            $syslogTarget->export();
        }

        /**
         * @param $name
         * @param $arguments
         * @return mixed
         */
        public static function __callStatic($name, $arguments)
        {
            if (isset(static::$functions[$name]) && is_callable(static::$functions[$name])) {
                $arguments = $arguments[0] ?? $arguments;
                return forward_static_call(static::$functions[$name], $arguments);
            }
            static::fail("Function '$name' has not implemented yet!");
        }

        /**
         * @covers Yiisoft\Log\Target\Syslog\SyslogTarget::formatMessage()
         */
        public function testFormatMessageWhereTextIsString(): void
        {
            $message = [LogLevel::INFO, 'text', ['category' => 'category', 'time' => 'timestamp']];

            $this->syslogTarget
                ->expects($this->once())
                ->method('getMessagePrefix')
                ->with($this->equalTo($message))
                ->willReturn('some prefix');

            $result = $this->syslogTarget->formatMessage($message);
            $this->assertEquals('some prefix[info][category] text', $result);
        }

        /**
         * @covers Yiisoft\Log\Target\Syslog\SyslogTarget::formatMessage()
         */
        public function testFormatMessageWhereTextIsException(): void
        {
            $exception = new \Exception('exception text');
            $message = [LogLevel::INFO, $exception, ['category' => 'category', 'time' => 'timestamp']];

            $this->syslogTarget
                ->expects($this->once())
                ->method('getMessagePrefix')
                ->with($this->equalTo($message))
                ->willReturn('some prefix');

            $result = $this->syslogTarget->formatMessage($message);
            $this->assertEquals('some prefix[info][category] ' . (string)$exception, $result);
        }
    }
}
