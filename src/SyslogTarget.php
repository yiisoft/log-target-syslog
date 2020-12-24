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
 * SyslogTarget writes log to syslog.
 */
final class SyslogTarget extends Target
{
    /**
     * @var string The openlog identity. This is a bitfield passed as the `$ident` parameter to `openlog()`.
     *
     * @see https://www.php.net/openlog
     */
    private string $identity;

    /**
     * @var int The openlog options. This is a bitfield passed as the `$option` parameter to `openlog()`.
     *
     * Defaults to `LOG_ODELAY | LOG_PID`.
     *
     * @see https://www.php.net/openlog
     */
    private int $options;

    /**
     * @var int The openlog facility. his is a bitfield passed as the `$facility` parameter to `openlog()`.
     *
     * Defaults to `LOG_USER`.
     *
     * @see https://www.php.net/openlog
     */
    private int $facility;

    /**
     * @var array The syslog levels.
     */
    private array $syslogLevels = [
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
     * @param string $identity The openlog identity. This is a bitfield passed as the `$ident` parameter to `openlog()`.
     * @param int $options The openlog options. This is a bitfield passed as the `$option` parameter to `openlog()`.
     * @param int $facility The openlog facility. his is a bitfield passed as the `$facility` parameter to `openlog()`.
     */
    public function __construct(string $identity, int $options = LOG_ODELAY | LOG_PID, int $facility = LOG_USER)
    {
        $this->identity = $identity;
        $this->options = $options;
        $this->facility = $facility;
        parent::__construct();

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
            if (syslog($this->syslogLevels[$message->level()], $formattedMessages[$key]) === false) {
                throw new RuntimeException('Unable to export log through system log.');
            }
        }

        closelog();
    }
}
