<?php

namespace Builderius\MooMoo\Platform\Bundle\OptionBundle;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\Bundle\Bundle;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\DependencyInjection\CompilerPass\KernelCompilerPass;
use Builderius\MooMoo\Platform\Bundle\OptionBundle\Registrator\OptionsRegistratorInterface;
use Builderius\Symfony\Component\DependencyInjection\ContainerBuilder;
class OptionBundle extends \Builderius\MooMoo\Platform\Bundle\KernelBundle\Bundle\Bundle
{
    /**
     * @inheritDoc
     */
    public function build(\Builderius\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        parent::build($container);
        /** @var ContainerBuilder $container */
        $container->addCompilerPass(new \Builderius\MooMoo\Platform\Bundle\KernelBundle\DependencyInjection\CompilerPass\KernelCompilerPass('moomoo_option', 'moomoo_option.registrator.main', 'addOption'));
    }
    /**
     * @inheritDoc
     */
    public function boot()
    {
        /** @var OptionsRegistratorInterface $optionsRegistrator */
        $optionsRegistrator = $this->container->get('moomoo_option.registrator.main');
        $optionsRegistrator->registerOptions();
        parent::boot();
    }
}
