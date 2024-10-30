<?php

namespace Builderius\Bundle\ModuleBundle\Checker\Chain\Element;

use Builderius\Bundle\ModuleBundle\Model\BuilderiusModuleInterface;

class BaseBuilderiusModuleCheckerChainElement extends AbstractBuilderiusModuleCheckerChainElement
{
    /**
     * @inheritDoc
     */
    public function checkModule(BuilderiusModuleInterface $module)
    {
        if (!$module->getName()) {
            throw new \Exception(sprintf(
                'There is no required property "name" for module %s',
                $module->getName()
            ));
        }
        if (strpos($module->getName(), ' ') !== false) {
            throw new \Exception("Module name can't contain spaces");
        }
        if (sanitize_text_field($module->getName()) !== $module->getName()) {
            throw new \Exception("Module name did not pass 'sanitize_text_field'");
        }
        if (!$module->getLabel()) {
            throw new \Exception(sprintf(
                'There is no required property "label" for module %s',
                $module->getName()
            ));
        }
        if (sanitize_text_field($module->getLabel()) !== $module->getLabel()) {
            throw new \Exception("Module label did not pass 'sanitize_text_field'");
        }
        if (!$module->getHtmlTemplate()) {
            throw new \Exception(sprintf(
                'There is no required property "htmlTemplate" for module %s',
                $module->getName()
            ));
        }
        
        return true;
    }
}
