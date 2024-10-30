<?php

namespace Builderius\Bundle\TemplateBundle\Checker\ContentConfig\Chain\Element;

class BaseBuilderiusTemplateContentConfigCheckerChainElement extends
AbstractBuilderiusTemplateContentConfigCheckerChainElement
{
    /**
     * @inheritDoc
     */
    protected function evaluate(array $configItem, $templateType, $templateTechnology)
    {
        $missingRequiredAttributes = [];
        if (!isset($configItem['id'])) {
            $missingRequiredAttributes[] = 'id';
        }
        if (!isset($configItem['name'])) {
            $missingRequiredAttributes[] = 'name';
        }
        if (isset($configItem['settings'])) {
            foreach ($configItem['settings'] as $index => $setting) {
                if (!isset($setting['name'])) {
                    $missingRequiredAttributes[] = sprintf('settings[%d][name]', $index);
                }
                if (!isset($setting['value'])) {
                    $missingRequiredAttributes[] = sprintf('settings[%d][value]', $index);
                }
            }
        }

        if (!empty($missingRequiredAttributes)) {
            throw new \Exception(
                sprintf(
                    'There is no required properties "%s" in builderius template config item',
                    implode(', ', $missingRequiredAttributes)
                )
            );
        }

        return true;
    }
}
