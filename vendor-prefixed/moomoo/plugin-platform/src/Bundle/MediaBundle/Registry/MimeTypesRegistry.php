<?php

namespace Builderius\MooMoo\Platform\Bundle\MediaBundle\Registry;

use Builderius\MooMoo\Platform\Bundle\MediaBundle\Model\MimeType;
use Builderius\MooMoo\Platform\Bundle\MediaBundle\Model\MimeTypeInterface;
class MimeTypesRegistry implements \Builderius\MooMoo\Platform\Bundle\MediaBundle\Registry\MimeTypesRegistryInterface
{
    /**
     * @var MimeTypeInterface[]
     */
    private $mimeTypes = [];
    /**
     * @inheritDoc
     */
    public function getMimeTypes()
    {
        if (empty($this->mimeTypes)) {
            foreach (get_allowed_mime_types() as $extension => $type) {
                $this->mimeTypes[$extension] = new \Builderius\MooMoo\Platform\Bundle\MediaBundle\Model\MimeType([\Builderius\MooMoo\Platform\Bundle\MediaBundle\Model\MimeType::EXTENSION_FIELD => $extension, \Builderius\MooMoo\Platform\Bundle\MediaBundle\Model\MimeType::MIME_TYPE_FIELD => $type]);
            }
        }
        return $this->mimeTypes;
    }
    /**
     * @inheritDoc
     */
    public function getMimeType($extension)
    {
        if ($this->hasMimeType($extension)) {
            return $this->getMimeTypes()[$extension];
        }
        return null;
    }
    /**
     * @inheritDoc
     */
    public function hasMimeType($extension)
    {
        $mimeTypes = $this->getMimeTypes();
        if (isset($mimeTypes[$extension])) {
            return \true;
        }
        return \false;
    }
}
