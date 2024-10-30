<?php

namespace Builderius\MooMoo\Platform\Bundle\RequestBundle\Registry;

use Builderius\MooMoo\Platform\Bundle\RequestBundle\Handler\RequestHandlerInterface;
interface RequestHandlersRegistryInterface
{
    /**
     * @return RequestHandlerInterface[]
     */
    public function getHandlers();
    /**
     * @param string $actionName
     * @return RequestHandlerInterface
     */
    public function getHandler($actionName);
    /**
     * @param string $actionName
     * @return bool
     */
    public function hasHandler($actionName);
}
