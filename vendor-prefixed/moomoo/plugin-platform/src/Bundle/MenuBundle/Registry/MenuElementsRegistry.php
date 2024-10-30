<?php

namespace Builderius\MooMoo\Platform\Bundle\MenuBundle\Registry;

use Builderius\MooMoo\Platform\Bundle\MenuBundle\Model\MenuElementInterface;
use Builderius\Symfony\Component\DependencyInjection\ContainerInterface;
class MenuElementsRegistry implements \Builderius\MooMoo\Platform\Bundle\MenuBundle\Registry\MenuElementsRegistryInterface
{
    /**
     * @var MenuElementInterface[]
     */
    private $menuElements = [];
    /**
     * @param MenuElementInterface $menuElement
     */
    public function addMenuElement(\Builderius\MooMoo\Platform\Bundle\MenuBundle\Model\MenuElementInterface $menuElement)
    {
        $this->menuElements[] = $menuElement;
    }
    /**
     * @inheritDoc
     */
    public function getMenuElements()
    {
        \usort($this->menuElements, function (\Builderius\MooMoo\Platform\Bundle\MenuBundle\Model\MenuElementInterface $a, \Builderius\MooMoo\Platform\Bundle\MenuBundle\Model\MenuElementInterface $b) {
            if (!$a->getParent() && $b->getParent() || $a->getIdentifier() === $b->getParent()) {
                return -1;
            } elseif ($a->getParent() && !$b->getParent() || $b->getIdentifier() === $a->getParent()) {
                return 1;
            } else {
                return 0;
            }
        });
        return $this->menuElements;
    }
    /**
     * @inheritDoc
     */
    public function getMenuElement($identifier)
    {
        if ($this->hasMenuElement($identifier)) {
            return $this->menuElements[$identifier];
        }
        return null;
    }
    /**
     * @inheritDoc
     */
    public function hasMenuElement($identifier)
    {
        return isset($this->menuElements[$identifier]);
    }
}
