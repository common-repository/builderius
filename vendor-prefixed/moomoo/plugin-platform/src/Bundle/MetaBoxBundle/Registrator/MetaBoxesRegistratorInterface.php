<?php

namespace Builderius\MooMoo\Platform\Bundle\MetaBoxBundle\Registrator;

use Builderius\MooMoo\Platform\Bundle\MetaBoxBundle\Model\MetaBoxInterface;
interface MetaBoxesRegistratorInterface
{
    /**
     * @param MetaBoxInterface[] $metaBoxes
     */
    public function registerMetaBoxes(array $metaBoxes);
}
