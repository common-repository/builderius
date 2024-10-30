<?php

namespace Builderius\Bundle\SettingBundle\EventListener;

use Builderius\Bundle\DeliverableBundle\Provider\BuilderiusDeliverableProviderInterface;
use Builderius\Bundle\TemplateBundle\Event\ConfigContainingEvent;
use Builderius\Bundle\TemplateBundle\Provider\Content\Template\BuilderiusTemplateAtRulesContentProvider;
use Builderius\Bundle\TemplateBundle\Provider\DeliverableTemplateSubModule\DeliverableTemplateSubModuleProviderInterface;
use Builderius\Bundle\TemplateBundle\Provider\DeliverableTemplateSubModule\DeliverableTemplateSubModulesProviderInterface;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\EventDispatcher\ConditionAwareEventListener;

class AtRulesRegistrationInFrontendModeEventListener extends ConditionAwareEventListener
{
    /**
     * @var BuilderiusDeliverableProviderInterface
     */
    private $deliverableProvider;

    /**
     * @var DeliverableTemplateSubModuleProviderInterface
     */
    private $dtsmProvider;

    /**
     * @var DeliverableTemplateSubModulesProviderInterface
     */
    private $dhtsmsProvider;

    /**
     * @param BuilderiusDeliverableProviderInterface $deliverableProvider
     * @param DeliverableTemplateSubModuleProviderInterface $dtsmProvider
     * @param DeliverableTemplateSubModulesProviderInterface $dhtsmsProvider
     */
    public function __construct(
        BuilderiusDeliverableProviderInterface $deliverableProvider,
        DeliverableTemplateSubModuleProviderInterface $dtsmProvider,
        DeliverableTemplateSubModulesProviderInterface $dhtsmsProvider
    ) {
        $this->deliverableProvider = $deliverableProvider;
        $this->dtsmProvider = $dtsmProvider;
        $this->dhtsmsProvider = $dhtsmsProvider;
    }

    /**
     * @param ConfigContainingEvent $event
     * @throws \Exception
     */
    public function beforeAtRulesRegistration(ConfigContainingEvent $event)
    {
        $atRules = $event->getConfig();
        $templateSubModule = $this->dtsmProvider->getTemplateSubModule();
        if (!$templateSubModule) {
            $hookTemplateSubModules = $this->dhtsmsProvider->getTemplateSubModules();
            if (!empty($hookTemplateSubModules)) {
                $templateSubModule = reset($hookTemplateSubModules);
            }
        }
        if ($templateSubModule) {
            $technology = $templateSubModule->getTechnology();
            $deliverable = $this->deliverableProvider->getDeliverable();
            $gssAll = $deliverable->getSubModules('global_settings_set', $technology);
            if (!empty($gssAll)) {
                $gssAll = reset($gssAll);
                if ($atRulesContent = $gssAll->getContent(BuilderiusTemplateAtRulesContentProvider::CONTENT_TYPE)) {
                    $atRulesContent = is_array($atRulesContent) ? $atRulesContent : [];
                    foreach($atRulesContent as $atRuleContent) {
                        if (!in_array($atRuleContent[BuilderiusTemplateAtRulesContentProvider::URL], $atRules)) {
                            $atRules[] = $atRuleContent[BuilderiusTemplateAtRulesContentProvider::URL];
                        }
                    }
                }
            }
        }
        $event->setConfig($atRules);
    }
}