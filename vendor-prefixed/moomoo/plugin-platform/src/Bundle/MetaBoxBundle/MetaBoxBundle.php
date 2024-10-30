<?php

namespace Builderius\MooMoo\Platform\Bundle\MetaBoxBundle;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\Bundle\Bundle;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\DependencyInjection\CompilerPass\KernelCompilerPass;
use Builderius\MooMoo\Platform\Bundle\MetaBoxBundle\Registrator\MetaBoxesRegistratorInterface;
use Builderius\MooMoo\Platform\Bundle\MetaBoxBundle\Registry\MetaBoxesRegistryInterface;
use Builderius\Symfony\Component\DependencyInjection\ContainerBuilder;
class MetaBoxBundle extends \Builderius\MooMoo\Platform\Bundle\KernelBundle\Bundle\Bundle
{
    /**
     * @inheritDoc
     */
    public function build(\Builderius\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new \Builderius\MooMoo\Platform\Bundle\KernelBundle\DependencyInjection\CompilerPass\KernelCompilerPass('moomoo_meta_box', 'moomoo_meta_box.registry.meta_boxes', 'addMetaBox'));
    }
    /**
     * @inheritDoc
     */
    public function boot()
    {
        /** @var MetaBoxesRegistryInterface $metaBoxesRegistry */
        $metaBoxesRegistry = $this->container->get('moomoo_meta_box.registry.meta_boxes');
        /** @var MetaBoxesRegistratorInterface $metaBoxesRegistrator */
        $metaBoxesRegistrator = $this->container->get('moomoo_meta_box.registrator.meta_boxes');
        $metaBoxesRegistrator->registerMetaBoxes($metaBoxesRegistry->getMetaBoxes());
        parent::boot();
    }
}
