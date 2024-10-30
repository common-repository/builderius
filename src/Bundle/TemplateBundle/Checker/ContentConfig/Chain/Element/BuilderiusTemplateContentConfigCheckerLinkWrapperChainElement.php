<?php

namespace Builderius\Bundle\TemplateBundle\Checker\ContentConfig\Chain\Element;

class BuilderiusTemplateContentConfigCheckerLinkWrapperChainElement extends
AbstractBuilderiusTemplateContentConfigCheckerChainElement
{
    /**
     * @inheritDoc
     */
    protected function evaluate(array $configItem, $templateType, $templateTechnology)
    {
        if (!isset($configItem['settings']) || empty($configItem['settings']) ||
            !isset($configItem['children']) || empty($configItem['children'])
        ) {
            return true;
        }
        $linkWrapper = false;
        foreach ($configItem['settings'] as $settingConfig) {
            if ($settingConfig['name'] === 'isLinkWrapper' && isset($settingConfig['value']['a1']) &&
                $settingConfig['value']['a1'] === true) {
                $linkWrapper = true;
                break;
            }
        }
        if ($linkWrapper === true) {
            foreach ($configItem['children'] as $childConfig) {
                $this->evaluateChild($childConfig);
            }
        }

        return true;
    }

    /**
     * @param array $configItem
     * @throws \Exception
     */
    private function evaluateChild (array $configItem)
    {
        if ($configItem['name'] === 'Anchor') {
            throw new \Exception(
                'Module transformed into link can\'t have child Anchor module'
            );
        }
        if (isset($configItem['settings']) && !empty($configItem['settings'])) {
            foreach ($configItem['settings'] as $settingConfig) {
                if ($settingConfig['name'] === 'isLinkWrapper' && isset($settingConfig['value']['a1']) &&
                    $settingConfig['value']['a1'] === true) {
                    throw new \Exception(
                        'Module transformed into link can\'t have child module transformed into link'
                    );
                }
                if (
                    in_array(
                        $settingConfig['name'],
                        ['dataText', 'dataSummary', 'dataBlockquote', 'dataList', 'dataHeading']
                    ) &&
                    isset($settingConfig['value']['a1']) &&
                    (
                        strpos($settingConfig['value']['a1'], '</a>') !== false ||
                        strpos($settingConfig['value']['a1'], '<a') !== false
                    )
                ) {
                    throw new \Exception(
                        'Module transformed into link can\'t have child module with <a> tag in content'
                    );
                }
            }
        }
        if (isset($configItem['children']) && is_array($configItem['children'])) {
            foreach ($configItem['children'] as $childConfig) {
                $this->evaluateChild($childConfig);
            }
        }
    }
}
