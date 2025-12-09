<?php

declare(strict_types=1);

namespace Yiisoft\Log\Target\Syslog;

use Psr\Log\LogLevel;
use RuntimeException;
use Yiisoft\Log\Message;
use Yiisoft\Log\Target;

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

/**
 * `SyslogTarget` writes log to syslog.
 */
final class SyslogTarget extends Target
{
    private const SYSLOG_LEVELS = [
        LogLevel::EMERGENCY => LOG_EMERG,
        LogLevel::ALERT => LOG_ALERT,
        LogLevel::CRITICAL => LOG_CRIT,
        LogLevel::ERROR => LOG_ERR,
        LogLevel::WARNING => LOG_WARNING,
        LogLevel::NOTICE => LOG_NOTICE,
        LogLevel::INFO => LOG_INFO,
        LogLevel::DEBUG => LOG_DEBUG,
    ];

    /**
     * @param string $identity The string that is prefixed to each message.
     * @param int $options Bit options to be used when generating a log message.
     * @param int $facility Used to specify what type of program is logging the message. This allows you to specify
     * (in your machine's syslog configuration) how messages coming from different facilities will be handled.
     * @param array $levels The {@see \Psr\Log\LogLevel log message levels} that this target is interested in.
     *
     * @link https://www.php.net/openlog
     */
    public function __construct(
        private string $identity,
        private int $options = LOG_ODELAY | LOG_PID,
        private int $facility = LOG_USER,
        array $levels = []
    ) {
        parent::__construct($levels);

        $this->setFormat(static function (Message $message) {
            return "[{$message->level()}][{$message->context('category', '')}] {$message->message()}";
        });
    }

    /**
     * Writes log messages to syslog.
     *
     * @see https://www.php.net/openlog
     * @see https://www.php.net/syslog
     * @see https://www.php.net/closelog
     *
     * @throws RuntimeException If unable to export log through system log.
     */
    protected function export(): void
    {
        $formattedMessages = $this->getFormattedMessages();
        openlog($this->identity, $this->options, $this->facility);

        foreach ($this->getMessages() as $key => $message) {
            syslog(self::SYSLOG_LEVELS[$message->level()], $formattedMessages[$key]);
        }

        closelog();
    }
}
