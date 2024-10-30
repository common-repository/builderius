<?php

namespace Builderius\Bundle\BuilderBundle\Registry;

use Builderius\Bundle\BuilderBundle\Model\BuilderiusBuilderFormTabInterface;

class BuilderiusBuilderFormTabsRegistry implements BuilderiusBuilderFormTabsRegistryInterface
{
    /**
     * @var BuilderiusBuilderFormTabInterface[]
     */
    protected $tabs = [];

    /**
     * @param BuilderiusBuilderFormTabInterface $tab
     */
    public function addTab(BuilderiusBuilderFormTabInterface $tab)
    {
        $this->tabs[$tab->getName()] = $tab;
    }

    /**
     * {@inheritdoc}
     */
    public function getTabs()
    {
        usort($this->tabs, function (BuilderiusBuilderFormTabInterface $a, BuilderiusBuilderFormTabInterface $b) {
            $aSortOrder = $a->getSortOrder();
            $bSortOrder = $b->getSortOrder();
            if ($aSortOrder < $bSortOrder) {
                return -1;
            } elseif ($aSortOrder > $bSortOrder) {
                return 1;
            } else {
                return 0;
            }
        });

        return $this->tabs;
    }

    /**
     * {@inheritdoc}
     */
    public function getTab($name)
    {
        if ($this->hasTab($name)) {
            return $this->tabs[$name];
        }
        
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function hasTab($name)
    {
        return isset($this->tabs[$name]);
    }
}
