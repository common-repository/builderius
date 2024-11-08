<?php

namespace Builderius\MooMoo\Platform\Bundle\MediaBundle;

use Builderius\MooMoo\Platform\Bundle\HookBundle\Registrator\HooksRegistratorInterface;
use Builderius\MooMoo\Platform\Bundle\HookBundle\Registry\HooksRegistryInterface;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\Bundle\Bundle;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\DependencyInjection\CompilerPass\KernelCompilerPass;
use Builderius\MooMoo\Platform\Bundle\MediaBundle\Registrator\ImageSizesRegistratorInterface;
use Builderius\MooMoo\Platform\Bundle\MediaBundle\Registrator\MimeTypesRegistratorInterface;
use Builderius\Symfony\Component\DependencyInjection\ContainerBuilder;
class MediaBundle extends \Builderius\MooMoo\Platform\Bundle\KernelBundle\Bundle\Bundle
{
    /**
     * @inheritDoc
     */
    public function build(\Builderius\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new \Builderius\MooMoo\Platform\Bundle\KernelBundle\DependencyInjection\CompilerPass\KernelCompilerPass('moomoo_image_size', 'moomoo_media.registrator.image_sizes', 'addImageSize'));
        $container->addCompilerPass(new \Builderius\MooMoo\Platform\Bundle\KernelBundle\DependencyInjection\CompilerPass\KernelCompilerPass('moomoo_mime_type', 'moomoo_media.registrator.mime_types', 'addMimeType'));
    }
    /**
     * @inheritDoc
     */
    public function boot()
    {
        /** @var ImageSizesRegistratorInterface $imageSizesRegistrator */
        $imageSizesRegistrator = $this->container->get('moomoo_media.registrator.image_sizes');
        $imageSizesRegistrator->registerImageSizes();
        /** @var MimeTypesRegistratorInterface $mimeTypesRegistrator */
        $mimeTypesRegistrator = $this->container->get('moomoo_media.registrator.mime_types');
        $mimeTypesRegistrator->registerMimeTypes();
        parent::boot();
    }
}
