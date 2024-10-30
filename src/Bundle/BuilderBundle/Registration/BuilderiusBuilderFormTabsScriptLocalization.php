<?php

namespace Builderius\Bundle\BuilderBundle\Registration;

use Builderius\Bundle\BuilderBundle\Model\BuilderiusBuilderFormTab;
use Builderius\Bundle\BuilderBundle\Registry\BuilderiusBuilderFormTabsRegistryInterface;

class BuilderiusBuilderFormTabsScriptLocalization extends AbstractBuilderiusBuilderScriptLocalization
{
    const PROPERTY_NAME = 'tabs';

    /**
     * @var BuilderiusBuilderFormTabsRegistryInterface
     */
    private $formTabsRegistry;

    /**
     * @param BuilderiusBuilderFormTabsRegistryInterface $formTabsRegistry
     */
    public function __construct(BuilderiusBuilderFormTabsRegistryInterface $formTabsRegistry)
    {
        $this->formTabsRegistry = $formTabsRegistry;
    }

    /**
     * @inheritDoc
     */
    public function getPropertyData()
    {
        $data = [];
        foreach ($this->formTabsRegistry->getTabs() as $tab) {
            $data[$tab->getName()] = [
                BuilderiusBuilderFormTab::NAME_FIELD => $tab->getName(),
                BuilderiusBuilderFormTab::LABEL_FIELD => $tab->getLabel(),
                BuilderiusBuilderFormTab::SORT_ORDER_FIELD => $tab->getSortOrder()
            ];
        }

        return $data;
    }
}
