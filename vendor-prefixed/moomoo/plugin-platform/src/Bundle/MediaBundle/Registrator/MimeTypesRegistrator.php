<?php

namespace Builderius\MooMoo\Platform\Bundle\MediaBundle\Registrator;

use Builderius\MooMoo\Platform\Bundle\MediaBundle\Model\MimeTypeInterface;
class MimeTypesRegistrator implements \Builderius\MooMoo\Platform\Bundle\MediaBundle\Registrator\MimeTypesRegistratorInterface
{
    /**
     * @var MimeTypeInterface[]
     */
    private $mimeTypes = [];
    /**
     * @param MimeTypeInterface $mimeType
     * @return $this
     */
    public function addMimeType(\Builderius\MooMoo\Platform\Bundle\MediaBundle\Model\MimeTypeInterface $mimeType)
    {
        $this->mimeTypes[$mimeType->getExtension()] = $mimeType;
        return $this;
    }
    /**
     * @inheritDoc
     */
    public function registerMimeTypes()
    {
        $mimeTypes = $this->mimeTypes;
        add_filter('upload_mimes', function ($existingMimes) use($mimeTypes) {
            foreach ($mimeTypes as $mimeType) {
                $existingMimes[$mimeType->getExtension()] = $mimeType->getMimeType();
            }
            return $existingMimes;
        });
    }
}
