<?php

declare(strict_types=1);

namespace Yiisoft\Log\Target\Syslog;

use Psr\Log\LogLevel;
use Yiisoft\Log\Logger;
use Yiisoft\Log\LogRuntimeException;
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
     * @see http://php.net/openlog for available options.
     */
    private int $options = LOG_ODELAY | LOG_PID;

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

    /**
     * Writes log messages to syslog.
     * Starting from version 2.0.14, this method throws LogRuntimeException in case the log can not be exported.
     * @throws LogRuntimeException
     * @throws \Throwable
     */
    public function export(): void
    {
        openlog($this->identity, $this->options, $this->facility);
        foreach ($this->getMessages() as $message) {
            if (syslog($this->syslogLevels[$message[0]], $this->formatMessage($message)) === false) {
                throw new LogRuntimeException('Unable to export log through system log!');
            }
        }
        closelog();
    }

    /**
     * {@inheritdoc}
     * @throws \Throwable
     */
    public function formatMessage(array $message): string
    {
        [$level, $text, $context] = $message;
        $level = Logger::getLevelName($level);
        $prefix = $this->getMessagePrefix($message);
        return $prefix . '[' . $level . '][' . ($context['category'] ?? '') . '] ' . $text;
    }
}
