<?php

namespace Builderius\Bundle\TemplateBundle\Provider\DeliverableTemplateSubModule;

use Builderius\Bundle\DeliverableBundle\Model\BuilderiusDeliverableSubModuleInterface;

interface DeliverableTemplateSubModulesProviderInterface
{

    /**
     * @return BuilderiusDeliverableSubModuleInterface[]
     * @throws \Exception
     */
    public function getTemplateSubModules();
}