<?php

namespace Builderius\MooMoo\Platform\Bundle\MediaBundle\Model;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag;
class ImageSize extends \Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag implements \Builderius\MooMoo\Platform\Bundle\MediaBundle\Model\ImageSizeInterface
{
    const NAME_FIELD = 'name';
    const WIDTH_FIELD = 'width';
    const HEIGHT_FIELD = 'height';
    const CROP_FIELD = 'crop';
    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->get(self::NAME_FIELD);
    }
    /**
     * @inheritDoc
     */
    public function getWidth()
    {
        return $this->get(self::WIDTH_FIELD);
    }
    /**
     * @inheritDoc
     */
    public function getHeight()
    {
        return $this->get(self::HEIGHT_FIELD);
    }
    /**
     * @inheritDoc
     */
    public function getCrop()
    {
        return $this->get(self::CROP_FIELD, \false);
    }
}
