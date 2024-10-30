<?php

namespace Builderius\MooMoo\Platform\Bundle\MenuBundle\Registrator;

use Exception;
use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareInterface;
use Builderius\MooMoo\Platform\Bundle\MenuBundle\Model\AdminMenuPageInterface;
use Builderius\MooMoo\Platform\Bundle\MenuBundle\Model\MenuElementInterface;
use Builderius\MooMoo\Platform\Bundle\PageBundle\Registry\PagesRegistryInterface;
class AdminMenuPagesRegistrator implements \Builderius\MooMoo\Platform\Bundle\MenuBundle\Registrator\MenuElementsRegistratorInterface
{
    /**
     * @var PagesRegistryInterface
     */
    private $pagesRegistry;
    /**
     * @param PagesRegistryInterface $pagesRegistry
     */
    public function __construct(\Builderius\MooMoo\Platform\Bundle\PageBundle\Registry\PagesRegistryInterface $pagesRegistry)
    {
        $this->pagesRegistry = $pagesRegistry;
    }
    /**
     * @param AdminMenuPageInterface[]|MenuElementInterface[] $menuElements
     * @inheritDoc
     */
    public function register(array $menuElements)
    {
        add_action('init', function () use($menuElements) {
            foreach ($menuElements as $menuElement) {
                if (!$menuElement instanceof \Builderius\MooMoo\Platform\Bundle\MenuBundle\Model\AdminMenuPageInterface) {
                    throw new \Exception('AdminMenuPagesRegistrator can register just AdminMenuPageInterface');
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
                        return;
                    }
                    $this->addNode($menuElement);
                } else {
                    $this->addNode($menuElement);
                }
            }
        });
    }
    /**
     * @param AdminMenuPageInterface $menuElement
     */
    private function addNode(\Builderius\MooMoo\Platform\Bundle\MenuBundle\Model\AdminMenuPageInterface $menuElement)
    {
        $iconUrl = $menuElement->getIconUrl() ?: '';
        $function = $menuElement->getPage() ? [$this->pagesRegistry->getPage($menuElement->getPage()), 'render'] : '';
        add_action('admin_menu', function () use($menuElement, $iconUrl, $function) {
            if (!$menuElement->getParent()) {
                add_menu_page($menuElement->getPageTitle(), $menuElement->getTitle(), $menuElement->getCapability(), $menuElement->getMenuSlug(), $function, $iconUrl, $menuElement->getPosition());
            } else {
                add_submenu_page($menuElement->getParent(), $menuElement->getPageTitle(), $menuElement->getTitle(), $menuElement->getCapability(), $menuElement->getMenuSlug(), $function, $menuElement->getPosition());
            }
        });
    }
}
