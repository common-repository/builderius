<?php

namespace Builderius\Mustache\Cache;

use Builderius\Mustache\Cache;
use Builderius\Mustache\Exception\InvalidArgumentException;
use Builderius\Mustache\Logger;
/**
 * Abstract Mustache Cache class.
 *
 * Provides logging support to child implementations.
 *
 * @abstract
 */
abstract class AbstractCache implements \Builderius\Mustache\Cache
{
    private $logger = null;
    /**
     * Get the current logger instance.
     *
     * @return Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }
    /**
     * Set a logger instance.
     *
     * @param Logger $logger
     */
    public function setLogger($logger = null)
    {
        if ($logger !== null && !($logger instanceof \Builderius\Mustache\Logger || \is_a($logger, 'Builderius\\Psr\\Log\\LoggerInterface'))) {
            throw new \Builderius\Mustache\Exception\InvalidArgumentException('Expected an instance of Logger or Psr\\Log\\LoggerInterface.');
        }
        $this->logger = $logger;
    }
    /**
     * Add a log record if logging is enabled.
     *
     * @param string $level   The logging level
     * @param string $message The log message
     * @param array  $context The log context
     */
    protected function log($level, $message, array $context = array())
    {
        if (isset($this->logger)) {
            $this->logger->log($level, $message, $context);
        }
    }
}
