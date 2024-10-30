<?php

namespace Builderius\MooMoo\Platform\Bundle\TaxonomyBundle;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\Bundle\Bundle;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\DependencyInjection\CompilerPass\KernelCompilerPass;
use Builderius\MooMoo\Platform\Bundle\TaxonomyBundle\Registrator\TaxonomiesRegistratorInterface;
use Builderius\MooMoo\Platform\Bundle\TaxonomyBundle\Registry\TaxonomiesRegistryInterface;
use Builderius\Symfony\Component\DependencyInjection\ContainerBuilder;
class TaxonomyBundle extends \Builderius\MooMoo\Platform\Bundle\KernelBundle\Bundle\Bundle
{
    /**
     * @inheritDoc
     */
    public function build(\Builderius\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new \Builderius\MooMoo\Platform\Bundle\KernelBundle\DependencyInjection\CompilerPass\KernelCompilerPass('moomoo_taxonomy', 'moomoo_taxonomy.registry.taxonomies', 'addTaxonomy'));
    }
    /**
     * @inheritDoc
     */
    public function boot()
    {
        /** @var TaxonomiesRegistryInterface $restMetaFieldProvidersRegistry */
        $taxonomiesRegistry = $this->container->get('moomoo_taxonomy.registry.taxonomies');
        /** @var TaxonomiesRegistratorInterface $taxonomiesRegistrator */
        $taxonomiesRegistrator = $this->container->get('moomoo_taxonomy.registrator.taxonomies');
        $taxonomiesRegistrator->registerTaxonomies($taxonomiesRegistry->getTaxonomies());
        parent::boot();
    }
}
