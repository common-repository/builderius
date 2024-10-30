<?php

namespace Builderius\MooMoo\Platform\Bundle\MenuBundle\Registrator;

use Exception;
use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareInterface;
use Builderius\MooMoo\Platform\Bundle\MenuBundle\Model\AdminBarNodeInterface;
use Builderius\MooMoo\Platform\Bundle\MenuBundle\Model\MenuElementInterface;
use Builderius\WP_Admin_Bar;
class AdminBarNodesRegistrator implements \Builderius\MooMoo\Platform\Bundle\MenuBundle\Registrator\MenuElementsRegistratorInterface
{
    /**
     * @param AdminBarNodeInterface[]|MenuElementInterface[] $menuElements
     * @inheritDoc
     */
    public function register(array $menuElements)
    {
        add_action('init', function () use($menuElements) {
            foreach ($menuElements as $menuElement) {
                if (!$menuElement instanceof \Builderius\MooMoo\Platform\Bundle\MenuBundle\Model\AdminBarNodeInterface) {
                    throw new \Exception('AdminBarNodesRegistrator can register just AdminBarNodeInterface');
                }
                if ($menuElement instanceof \Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareInterface && $menuElement->hasConditions()) {
                    $evaluated = \true;
                    foreach ($menuElement->getConditions() as $condition) {
                        if ($condition->evaluate() === \false) {
                            $evaluated = \false;
                            break;
                        }
                    }
                    if (!$evaluated) {
                        continue;
                    }
                    $this->addNode($menuElement);
                } else {
                    $this->addNode($menuElement);
                }
            }
        });
    }
    /**
     * @param AdminBarNodeInterface $menuElement
     */
    private function addNode(\Builderius\MooMoo\Platform\Bundle\MenuBundle\Model\AdminBarNodeInterface $menuElement)
    {
        add_action('admin_bar_menu', function (\WP_Admin_Bar $wp_admin_bar) use($menuElement) {
            $wp_admin_bar->add_node(['parent' => $menuElement->getParent(), 'id' => $menuElement->getIdentifier(), 'title' => $menuElement->getTitle(), 'href' => $menuElement->getHref(), 'meta' => $menuElement->getMeta(), 'group' => $menuElement->isGroup()]);
        }, 1000);
    }
}
