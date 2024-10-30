<?php

namespace Builderius\Mustache\Exception;

use Builderius\Mustache\Exception;
/**
 * Unknown filter exception.
 */
class UnknownFilterException extends \UnexpectedValueException implements \Builderius\Mustache\Exception
{
    protected $filterName;
    /**
     * @param string    $filterName
     * @param Exception $previous
     */
    public function __construct($filterName, \Exception $previous = null)
    {
        $this->filterName = $filterName;
        $message = \sprintf('Unknown filter: %s', $filterName);
        if (\version_compare(\PHP_VERSION, '5.3.0', '>=')) {
            parent::__construct($message, 0, $previous);
        } else {
            parent::__construct($message);
            // @codeCoverageIgnore
        }
    }
    public function getFilterName()
    {
        return $this->filterName;
    }
}
