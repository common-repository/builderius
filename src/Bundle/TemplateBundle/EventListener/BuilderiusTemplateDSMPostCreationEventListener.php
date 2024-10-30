<?php

namespace Builderius\Bundle\TemplateBundle\EventListener;

use Builderius\Bundle\DeliverableBundle\Event\BuilderiusDSMPostCreationEvent;
use Builderius\Bundle\TemplateBundle\Model\BuilderiusTemplate;
use Builderius\Bundle\TemplateBundle\Model\BuilderiusTemplateInterface;
use Builderius\Bundle\TemplateBundle\Provider\TemplateType\BuilderiusTemplateTypesProviderInterface;

class BuilderiusTemplateDSMPostCreationEventListener
{
    /**
     * @var BuilderiusTemplateTypesProviderInterface
     */
    private $templateTypesProvider;

    /**
     * @param BuilderiusTemplateTypesProviderInterface $templateTypesProvider
     */
    public function __construct(
        BuilderiusTemplateTypesProviderInterface $templateTypesProvider
    ) {
        $this->templateTypesProvider = $templateTypesProvider;
    }

    /**
     * @param BuilderiusDSMPostCreationEvent $event
     */
    public function onDSMPostCreation(BuilderiusDSMPostCreationEvent $event)
    {
        $owner = $event->getVcsOwner();
        if ($owner instanceof BuilderiusTemplateInterface) {
            $type = $this->templateTypesProvider->getType($owner->getType());
            if ($type->isStandalone()) {
                $event
                    ->setType($owner->getSubType())
                    ->setEntityType('template')
                    ->setTitle($owner->getTitle());
                if ($owner->getSubType() === 'regular') {
                    $event->setAttributes(
                        [
                            BuilderiusTemplate::SORT_ORDER_FIELD => $owner->getSortOrder(),
                            BuilderiusTemplate::APPLY_RULES_CONFIG_FIELD => $owner->getApplyRulesConfig(),
                        ]
                    );
                } elseif ($owner->getSubType() === 'hook') {
                    $event->setAttributes(
                        [
                            BuilderiusTemplate::SORT_ORDER_FIELD => $owner->getSortOrder(),
                            BuilderiusTemplate::APPLY_RULES_CONFIG_FIELD => $owner->getApplyRulesConfig(),
                            BuilderiusTemplate::HOOK_FIELD => $owner->getHook(),
                            BuilderiusTemplate::HOOK_TYPE_FIELD => $owner->getHookType(),
                            BuilderiusTemplate::HOOK_ACCEPTED_ARGS_FIELD => $owner->getHookAcceptedArgs(),
                            BuilderiusTemplate::CLEAR_EXISTING_HOOKS_FIELD => $owner->isClearExistingHooks(),
                        ]
                    );
                }
                $event->stopPropagation();
            }
        }
    }
}