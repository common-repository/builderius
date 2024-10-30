<?php

namespace Builderius\Bundle\TemplateBundle\Provider\DeliverableTemplateSubModule;

use Builderius\Bundle\DeliverableBundle\Model\BuilderiusDeliverableSubModuleInterface;

interface DeliverableTemplateSubModuleProviderInterface
{

    /**
     * @return BuilderiusDeliverableSubModuleInterface
     * @throws \Exception
     */
    public function getTemplateSubModule();
}