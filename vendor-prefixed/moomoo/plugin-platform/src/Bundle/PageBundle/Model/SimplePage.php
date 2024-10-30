<?php

namespace Builderius\MooMoo\Platform\Bundle\PageBundle\Model;

class SimplePage extends \Builderius\MooMoo\Platform\Bundle\PageBundle\Model\AbstractPage
{
    /**
     * @var string
     */
    private $slug;
    /**
     * @var string
     */
    private $template;
    /**
     * @param $slug
     * @param $template
     */
    public function __construct($slug, $template)
    {
        $this->slug = $slug;
        $this->template = $template;
    }
    /**
     * @inheritDoc
     */
    public function getSlug()
    {
        return $this->slug;
    }
    /**
     * @inheritDoc
     */
    public function render()
    {
        echo $this->templating->render($this->template);
    }
}
