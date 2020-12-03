<?php

declare(strict_types=1);

namespace Yiisoft\Log\Target\Syslog;

use Psr\Log\LogLevel;
use RuntimeException;
use Yiisoft\Log\Message;
use Yiisoft\Log\Target;

/**
 * SyslogTarget writes log to syslog.
 */
class SyslogTarget extends Target
{
    /**
     * @var string syslog identity
     */
    private string $identity;

    /**
     * @var int syslog facility.
     */
    private int $facility = LOG_USER;

    /**
     * @var int openlog options. This is a bitfield passed as the `$option` parameter to [openlog()](http://php.net/openlog).
     * Defaults to `LOG_ODELAY | LOG_PID`.
     *
     * @see http://php.net/openlog for available options.
     */
    private int $options = LOG_ODELAY | LOG_PID;

    /**
     * @var bool Whether the message format was previously set.
     */
    private bool $isMessageFormatSet = false;

    /**
     * @var array syslog levels
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

    public function setIdentity(string $identity): self
    {
        $this->identity = $identity;
        return $this;
    }

    public function setFacility(int $facility): self
    {
        $this->facility = $facility;
        return $this;
    }

    public function setOptions(int $options): self
    {
        $this->options = $options;
        return $this;
    }

    public function setFormat(callable $format): Target
    {
        $this->isMessageFormatSet = true;
        return parent::setFormat($format);
    }

    /**
     * Writes log messages to syslog.
     * Starting from version 2.0.14, this method throws RuntimeException in case the log can not be exported.
     *
     * @throws RuntimeException
     */
    public function export(): void
    {
        $formattedMessages = $this->getFormattedMessages();
        openlog($this->identity, $this->options, $this->facility);

        if (!$this->isMessageFormatSet) {
            $this->setFormat(static function (Message $message) {
                return "[{$message->level()}][{$message->context('category', '')}] {$message->message()}";
            });
        }

        foreach ($this->getMessages() as $key => $message) {
            if (syslog($this->syslogLevels[$message->level()], $formattedMessages[$key]) === false) {
                throw new RuntimeException('Unable to export log through system log.');
            }
        }

        closelog();
    }
}
