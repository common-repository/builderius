<?php

namespace Builderius\MooMoo\Platform\Bundle\PageBundle\Registry;

use Builderius\MooMoo\Platform\Bundle\PageBundle\Model\PageInterface;
interface PagesRegistryInterface
{
    /**
     * @return PageInterface[]
     */
    public function getPages();
    /**
     * @param string $slug
     * @return PageInterface|null
     */
    public function getPage($slug);
    /**
     * @param string $slug
     * @return bool
     */
    public function hasPage($slug);
}
