<?php

namespace Builderius\MooMoo\Platform\Bundle\PageBundle\Model;

use Builderius\Symfony\Component\Templating\EngineInterface;
abstract class AbstractPage implements \Builderius\MooMoo\Platform\Bundle\PageBundle\Model\PageInterface
{
    /**
     * @var EngineInterface
     */
    protected $templating;
    /**
     * @param EngineInterface $templating
     */
    public function setTemplating(\Builderius\Symfony\Component\Templating\EngineInterface $templating)
    {
        $this->templating = $templating;
    }
}
