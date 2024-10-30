<?php

namespace Builderius\MooMoo\Platform\Bundle\MediaBundle\Model;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag;
class MimeType extends \Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag implements \Builderius\MooMoo\Platform\Bundle\MediaBundle\Model\MimeTypeInterface
{
    const EXTENSION_FIELD = 'extension';
    const MIME_TYPE_FIELD = 'mime_type';
    /**
     * @inheritDoc
     */
    public function getExtension()
    {
        return $this->get(self::EXTENSION_FIELD);
    }
    /**
     * @inheritDoc
     */
    public function getMimeType()
    {
        return $this->get(self::MIME_TYPE_FIELD);
    }
}
