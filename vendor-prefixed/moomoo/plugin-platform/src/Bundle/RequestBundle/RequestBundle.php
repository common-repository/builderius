<?php

namespace Builderius\MooMoo\Platform\Bundle\RequestBundle;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\Bundle\Bundle;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\DependencyInjection\CompilerPass\KernelCompilerPass;
use Builderius\MooMoo\Platform\Bundle\RequestBundle\Handler\Registrator\RequestHandlersRegistratorInterface;
use Builderius\MooMoo\Platform\Bundle\RequestBundle\Registry\RequestHandlersRegistryInterface;
use Builderius\Symfony\Component\DependencyInjection\ContainerBuilder;
class RequestBundle extends \Builderius\MooMoo\Platform\Bundle\KernelBundle\Bundle\Bundle
{
    /**
     * @inheritDoc
     */
    public function build(\Builderius\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new \Builderius\MooMoo\Platform\Bundle\KernelBundle\DependencyInjection\CompilerPass\KernelCompilerPass('moomoo_request_handler', 'moomoo_request.registry.request_handlers', 'addHandler'));
    }
    /**
     * @inheritDoc
     */
    public function boot()
    {
        /** @var RequestHandlersRegistryInterface $requestHandlersRegistry */
        $requestHandlersRegistry = $this->container->get('moomoo_request.registry.request_handlers');
        /** @var RequestHandlersRegistratorInterface $requestHandlersRegistrator */
        $requestHandlersRegistrator = $this->container->get('moomoo_request.handlers_registrator.main');
        $requestHandlersRegistrator->registerRequestHandlers($requestHandlersRegistry->getHandlers());
        parent::boot();
    }
}
