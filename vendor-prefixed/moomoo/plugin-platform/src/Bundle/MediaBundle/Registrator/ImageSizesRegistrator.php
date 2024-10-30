<?php

namespace Builderius\MooMoo\Platform\Bundle\MediaBundle\Registrator;

use Builderius\MooMoo\Platform\Bundle\MediaBundle\Model\ImageSizeInterface;
class ImageSizesRegistrator implements \Builderius\MooMoo\Platform\Bundle\MediaBundle\Registrator\ImageSizesRegistratorInterface
{
    /**
     * @var ImageSizeInterface[]
     */
    private $imageSizes = [];
    /**
     * @param ImageSizeInterface $imageSize
     * @return $this
     */
    public function addImageSize(\Builderius\MooMoo\Platform\Bundle\MediaBundle\Model\ImageSizeInterface $imageSize)
    {
        $this->imageSizes[$imageSize->getName()] = $imageSize;
        return $this;
    }
    /**
     * @inheritDoc
     */
    public function registerImageSizes()
    {
        $imageSizes = $this->imageSizes;
        add_action('after_setup_theme', function () use($imageSizes) {
            foreach ($imageSizes as $imageSize) {
                add_image_size($imageSize->getName(), $imageSize->getWidth(), $imageSize->getHeight(), $imageSize->getCrop());
            }
        });
    }
}
