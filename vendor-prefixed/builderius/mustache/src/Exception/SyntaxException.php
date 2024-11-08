<?php

namespace Builderius\Mustache\Exception;

use Builderius\Mustache\Exception;
/**
 * Mustache syntax exception.
 */
class SyntaxException extends \LogicException implements \Builderius\Mustache\Exception
{
    protected $token;
    /**
     * @param string    $msg
     * @param array     $token
     * @param Exception $previous
     */
    public function __construct($msg, array $token, \Exception $previous = null)
    {
        $this->token = $token;
        if (\version_compare(\PHP_VERSION, '5.3.0', '>=')) {
            parent::__construct($msg, 0, $previous);
        } else {
            parent::__construct($msg);
            // @codeCoverageIgnore
        }
    }
    /**
     * @return array
     */
    public function getToken()
    {
        return $this->token;
    }
}
