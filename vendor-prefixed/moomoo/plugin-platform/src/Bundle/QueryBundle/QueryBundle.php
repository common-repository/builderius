<?php

namespace Builderius\MooMoo\Platform\Bundle\QueryBundle;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\Bundle\Bundle;
use Builderius\MooMoo\Platform\Bundle\QueryBundle\DependencyInjection\CompilerPass\QueryCompilerPass;
use Builderius\Symfony\Component\DependencyInjection\ContainerBuilder;
class QueryBundle extends \Builderius\MooMoo\Platform\Bundle\KernelBundle\Bundle\Bundle
{
    /**
     * @inheritDoc
     */
    public function build(\Builderius\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new \Builderius\MooMoo\Platform\Bundle\QueryBundle\DependencyInjection\CompilerPass\QueryCompilerPass());
    }
}
