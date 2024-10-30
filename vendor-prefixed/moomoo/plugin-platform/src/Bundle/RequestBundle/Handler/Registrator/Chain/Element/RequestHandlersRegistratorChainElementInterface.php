<?php

namespace Builderius\MooMoo\Platform\Bundle\RequestBundle\Handler\Registrator\Chain\Element;

use Builderius\MooMoo\Platform\Bundle\RequestBundle\Handler\RequestHandlerInterface;
interface RequestHandlersRegistratorChainElementInterface
{
    /**
     * @param RequestHandlerInterface $handler
     * @return bool
     */
    public function isApplicable(\Builderius\MooMoo\Platform\Bundle\RequestBundle\Handler\RequestHandlerInterface $handler);
    /**
     * @param RequestHandlerInterface $handler
     */
    public function register(\Builderius\MooMoo\Platform\Bundle\RequestBundle\Handler\RequestHandlerInterface $handler);
}
