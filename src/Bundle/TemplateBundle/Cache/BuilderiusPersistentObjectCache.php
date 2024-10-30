<?php

namespace Builderius\Bundle\TemplateBundle\Cache;

use Builderius\Symfony\Component\Cache\Adapter\AbstractAdapter;
use Builderius\Symfony\Component\Cache\Marshaller\DefaultMarshaller;
use Builderius\Symfony\Component\Cache\PruneableInterface;
use Builderius\Symfony\Component\Cache\Traits\FilesystemTrait;

class BuilderiusPersistentObjectCache extends AbstractAdapter implements PruneableInterface
{
    use FilesystemTrait;

    public function __construct()
    {
        $this->marshaller = new DefaultMarshaller();
        parent::__construct('', 0);
        $this->init('', sprintf('%s/builderius/cache/persistent-objects/', wp_upload_dir()['basedir']));
    }

    public function __serialize()
    {
        return [];
    }

    public function __unserialize(array $data)
    {
        $this->__construct();
    }
}
