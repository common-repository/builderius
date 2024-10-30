<?php

namespace Builderius\MooMoo\Platform\Bundle\PostBundle;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\Bundle\Bundle;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\DependencyInjection\CompilerPass\KernelCompilerPass;
use Builderius\MooMoo\Platform\Bundle\PostBundle\PostStatus\Registrator\PostStatusesRegistratorInterface;
use Builderius\MooMoo\Platform\Bundle\PostBundle\PostStatus\Registry\PostStatusesRegistryInterface;
use Builderius\MooMoo\Platform\Bundle\PostBundle\PostType\Registrator\PostTypesRegistratorInterface;
use Builderius\MooMoo\Platform\Bundle\PostBundle\PostType\Registry\PostTypesRegistryInterface;
use Builderius\Symfony\Component\DependencyInjection\ContainerBuilder;
class PostBundle extends \Builderius\MooMoo\Platform\Bundle\KernelBundle\Bundle\Bundle
{
    /**
     * @inheritDoc
     */
    public function build(\Builderius\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new \Builderius\MooMoo\Platform\Bundle\KernelBundle\DependencyInjection\CompilerPass\KernelCompilerPass('moomoo_post_type', 'moomoo_post.registry.post_types', 'addPostType'));
        $container->addCompilerPass(new \Builderius\MooMoo\Platform\Bundle\KernelBundle\DependencyInjection\CompilerPass\KernelCompilerPass('moomoo_post_status', 'moomoo_post.registry.post_statuses', 'addPostStatus'));
    }
    /**
     * @inheritDoc
     */
    public function boot()
    {
        /** @var PostTypesRegistryInterface $postTypesRegistry */
        $postTypesRegistry = $this->container->get('moomoo_post.registry.post_types');
        /** @var PostTypesRegistratorInterface $postTypesRegistrator */
        $postTypesRegistrator = $this->container->get('moomoo_post.registrator.post_types');
        $postTypesRegistrator->registerPostTypes($postTypesRegistry->getPostTypes());
        /** @var PostStatusesRegistryInterface $postStatusesRegistry */
        $postStatusesRegistry = $this->container->get('moomoo_post.registry.post_statuses');
        /** @var PostStatusesRegistratorInterface $postStatusesRegistrator */
        $postStatusesRegistrator = $this->container->get('moomoo_post.registrator.post_statuses');
        $postStatusesRegistrator->registerPostStatuses($postStatusesRegistry->getPostStatuses());
        parent::boot();
    }
}
