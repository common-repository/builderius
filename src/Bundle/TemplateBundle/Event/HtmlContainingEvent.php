<?php

namespace Builderius\Bundle\TemplateBundle\Event;

use Builderius\Symfony\Contracts\EventDispatcher\Event;

class HtmlContainingEvent extends Event
{
    /**
     * @var string
     */
    private $html;

    /**
     * @var \WP_Error
     */
    private $error;

    /**
     * @param string $html
     */
    public function __construct($html)
    {
        $this->html = $html;
    }

    /**
     * @return string
     */
    public function getHtml()
    {
        return $this->html;
    }

    /**
     * @param string $html
     * @return $this
     */
    public function setHtml($html)
    {
        $this->html = $html;

        return $this;
    }

    /**
     * @return \WP_Error
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param \WP_Error $error
     * @return $this
     */
    public function setError(\WP_Error $error)
    {
        $this->error = $error;

        return $this;
    }
}