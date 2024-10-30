<?php

namespace Builderius\MooMoo\Platform\Bundle\RequestBundle\Handler\Registrator\Chain\Element;

use Builderius\MooMoo\Platform\Bundle\RequestBundle\Handler\Registrator\RequestHandlersRegistratorInterface;
abstract class AbstractRequestHandlersRegistratorChainElement implements \Builderius\MooMoo\Platform\Bundle\RequestBundle\Handler\Registrator\RequestHandlersRegistratorInterface, \Builderius\MooMoo\Platform\Bundle\RequestBundle\Handler\Registrator\Chain\Element\RequestHandlersRegistratorChainElementInterface
{
    /**
     * @var RequestHandlersRegistratorChainElementInterface|null
     */
    private $successor;
    /**
     * @inheritDoc
     */
    public function registerRequestHandlers(array $handlers)
    {
        foreach ($handlers as $handler) {
            if ($this->isApplicable($handler)) {
                $this->register($handler);
            } elseif ($this->getSuccessor() && $this->getSuccessor()->isApplicable($handler)) {
                $this->getSuccessor()->register($handler);
            } else {
                continue;
            }
        }
    }
    /**
     * @param RequestHandlersRegistratorChainElementInterface $handlerRegistrator
     */
    public function setSuccessor(\Builderius\MooMoo\Platform\Bundle\RequestBundle\Handler\Registrator\Chain\Element\RequestHandlersRegistratorChainElementInterface $handlerRegistrator)
    {
        $this->successor = $handlerRegistrator;
    }
    /**
     * @return RequestHandlersRegistratorChainElementInterface|null
     */
    protected function getSuccessor()
    {
        return $this->successor;
    }
}
