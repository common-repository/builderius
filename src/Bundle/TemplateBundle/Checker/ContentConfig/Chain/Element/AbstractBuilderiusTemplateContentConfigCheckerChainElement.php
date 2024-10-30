<?php

namespace Builderius\Bundle\TemplateBundle\Checker\ContentConfig\Chain\Element;

use Builderius\Bundle\TemplateBundle\Checker\ContentConfig\BuilderiusTemplateContentConfigCheckerInterface;
use Builderius\Bundle\TemplateBundle\Converter\ConfigToHierarchicalConfigConverter;

abstract class AbstractBuilderiusTemplateContentConfigCheckerChainElement implements
    BuilderiusTemplateContentConfigCheckerInterface
{
    /**
     * @var BuilderiusTemplateContentConfigCheckerInterface|null
     */
    private $successor;

    /**
     * @param BuilderiusTemplateContentConfigCheckerInterface $checker
     */
    public function setSuccessor(BuilderiusTemplateContentConfigCheckerInterface $checker)
    {
        $this->successor = $checker;
    }

    /**
     * @return BuilderiusTemplateContentConfigCheckerInterface|null
     */
    protected function getSuccessor()
    {
        return $this->successor;
    }

    /**
     * @inheritDoc
     */
    public function check(array $contentConfig)
    {
        if (empty($contentConfig)) {
            return true;
        }
        if (!isset($contentConfig['template']['type'])) {
            throw new \Exception(
                'There is no template type'
            );
        }
        if (!isset($contentConfig['template']['technology'])) {
            throw new \Exception(
                'There is no template technology'
            );
        }
        $templateType = $contentConfig['template']['type'];
        $templateTechnology = $contentConfig['template']['technology'];
        $hierarchicalConfig = ConfigToHierarchicalConfigConverter::convert($contentConfig);
        $result = true;
        if(is_array($hierarchicalConfig)) {
            foreach ($hierarchicalConfig as $configItem) {
                $result = $this->checkConfigItem($configItem, $templateType, $templateTechnology);
            }
        }

        if ($this->getSuccessor()) {
            return $this->getSuccessor()->check($contentConfig);
        } else {
            return $result;
        }
    }

    /**
     * @param array $configItem
     * @param string $templateType
     * @param string $templateTechnology
     * @return bool|mixed
     * @throws \Exception
     */
    protected function checkConfigItem(array $configItem, $templateType, $templateTechnology)
    {
        $result = $this->evaluate($configItem, $templateType, $templateTechnology);
        if (isset($configItem['children']) && !empty($configItem['children'])) {
            foreach ($configItem['children'] as $childItem) {
                $result = $this->checkConfigItem($childItem, $templateType, $templateTechnology);
            }
        }

        return $result;
    }

    /**
     * @param array $configItem
     * @param string $templateType
     * @param string $templateTechnology
     * @return bool
     * @throws \Exception
     */
    abstract protected function evaluate(array $configItem, $templateType, $templateTechnology);
}
