<?php

namespace Builderius\MooMoo\Platform\Bundle\RequestBundle\Handler\Registrator\Chain\Element;

use Builderius\MooMoo\Platform\Bundle\RequestBundle\Handler\RequestHandlerInterface;
class AjaxRequestHandlersRegistratorChainElement extends \Builderius\MooMoo\Platform\Bundle\RequestBundle\Handler\Registrator\Chain\Element\AbstractRequestHandlersRegistratorChainElement
{
    /**
     * @inheritDoc
     */
    public function isApplicable(\Builderius\MooMoo\Platform\Bundle\RequestBundle\Handler\RequestHandlerInterface $handler)
    {
        return $handler->isAjax();
    }
    /**
     * @inheritDoc
     */
    public function register(\Builderius\MooMoo\Platform\Bundle\RequestBundle\Handler\RequestHandlerInterface $handler)
    {
        $actionName = $handler->getActionName();
        add_action(\sprintf('wp_ajax_%s', $actionName), [$handler, 'handle']);
        if (!$handler->isPrivileged()) {
            add_action(\sprintf('wp_ajax_nopriv_%s', $actionName), [$handler, 'handle']);
        }
        return null;
    }
}
