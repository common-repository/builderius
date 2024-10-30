<?php

namespace Builderius\MooMoo\Platform\Bundle\ShortcodeBundle;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\Bundle\Bundle;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\DependencyInjection\CompilerPass\KernelCompilerPass;
use Builderius\MooMoo\Platform\Bundle\ShortcodeBundle\Registrator\ShortcodesRegistratorInterface;
use Builderius\Symfony\Component\DependencyInjection\ContainerBuilder;
class ShortcodeBundle extends \Builderius\MooMoo\Platform\Bundle\KernelBundle\Bundle\Bundle
{
    /**
     * @inheritDoc
     */
    public function build(\Builderius\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        parent::build($container);
        /** @var ContainerBuilder $container */
        $container->addCompilerPass(new \Builderius\MooMoo\Platform\Bundle\KernelBundle\DependencyInjection\CompilerPass\KernelCompilerPass('moomoo_shortcode', 'moomoo_shortcode.registrator.shortcodes', 'addShortcode'));
    }
    /**
     * @inheritDoc
     */
    public function boot()
    {
        /** @var ShortcodesRegistratorInterface $shortcodesRegistrator */
        $shortcodesRegistrator = $this->container->get('moomoo_shortcode.registrator.shortcodes');
        $shortcodesRegistrator->registerShortcodes();
        parent::boot();
    }
}
