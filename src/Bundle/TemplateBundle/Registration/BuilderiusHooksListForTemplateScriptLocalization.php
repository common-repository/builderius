<?php

namespace Builderius\Bundle\TemplateBundle\Registration;

use Builderius\Bundle\BuilderBundle\Registration\AbstractBuilderiusBuilderScriptLocalization;
use Builderius\Bundle\TemplateBundle\Model\BuilderiusTemplate;
use Builderius\Bundle\TemplateBundle\Provider\TemplateAcceptableHook\BuilderiusTemplateAcceptableHooksProviderInterface;

class BuilderiusHooksListForTemplateScriptLocalization extends AbstractBuilderiusBuilderScriptLocalization
{
    const PROPERTY_NAME = 'hooksList';

    /**
     * @var BuilderiusTemplateAcceptableHooksProviderInterface
     */
    private $templateAcceptableHooksProvider;

    /**
     * @param BuilderiusTemplateAcceptableHooksProviderInterface $templateAcceptableHooksProvider
     */
    public function __construct(
        BuilderiusTemplateAcceptableHooksProviderInterface $templateAcceptableHooksProvider
    ) {
        $this->templateAcceptableHooksProvider = $templateAcceptableHooksProvider;
    }

    /**
     * @inheritDoc
     */
    public function getPropertyData()
    {
        $hooks = [];
        foreach ($this->templateAcceptableHooksProvider->getAcceptableHooks() as $hook) {
            if (!isset($hooks[$hook->getType()])) {
                $hooks[$hook->getType()] = [];
            }
            $hooks[$hook->getType()][] = [
                BuilderiusTemplate::HOOK_FIELD => $hook->getName(),
                BuilderiusTemplate::HOOK_ACCEPTED_ARGS_FIELD => $hook->getAcceptedArgs()
            ];
        }
        foreach ($hooks as $type => $grHooks) {
            usort($grHooks, function($a, $b)
            {
                if ($a[BuilderiusTemplate::HOOK_FIELD] == $b[BuilderiusTemplate::HOOK_FIELD]) {
                    return 0;
                }
                return ($a[BuilderiusTemplate::HOOK_FIELD] < $b[BuilderiusTemplate::HOOK_FIELD]) ? -1 : 1;
            });
            $hooks[$type] = $grHooks;
        }

        return $hooks;
    }
}
