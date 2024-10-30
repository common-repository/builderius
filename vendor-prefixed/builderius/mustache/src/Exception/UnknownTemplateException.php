<?php

namespace Builderius\Mustache\Exception;

use Builderius\Mustache\Exception;
/**
 * Unknown template exception.
 */
class UnknownTemplateException extends \InvalidArgumentException implements \Builderius\Mustache\Exception
{
    protected $templateName;
    /**
     * @param string    $templateName
     * @param Exception $previous
     */
    public function __construct($templateName, \Exception $previous = null)
    {
        $this->templateName = $templateName;
        $message = \sprintf('Unknown template: %s', $templateName);
        if (\version_compare(\PHP_VERSION, '5.3.0', '>=')) {
            parent::__construct($message, 0, $previous);
        } else {
            parent::__construct($message);
            // @codeCoverageIgnore
        }
    }
    public function getTemplateName()
    {
        return $this->templateName;
    }
}
