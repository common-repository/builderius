<?php

namespace Builderius\Bundle\TemplateBundle\EventListener;

use Builderius\Bundle\TemplateBundle\Event\ConfigContainingEvent;
use Builderius\Bundle\TemplateBundle\Provider\Content\Template\BuilderiusTemplateAtRulesContentProvider;
use Builderius\Bundle\TemplateBundle\Provider\DeliverableTemplateSubModule\DeliverableTemplateSubModuleProviderInterface;
use Builderius\Bundle\TemplateBundle\Twig\TemplateDataVarsExtension;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Event\AssetsContainingEvent;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\Style;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\EventDispatcher\ConditionAwareEventListener;
use Builderius\Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AtRulesRegistrationInFrontendModeEventListener extends ConditionAwareEventListener
{
    /**
     * @var DeliverableTemplateSubModuleProviderInterface
     */
    private $dtsmProvider;

    /**
     * @var TemplateDataVarsExtension
     */
    private $twigExtension;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param DeliverableTemplateSubModuleProviderInterface $dtsmProvider
     * @param TemplateDataVarsExtension $twigExtension
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        DeliverableTemplateSubModuleProviderInterface $dtsmProvider,
        TemplateDataVarsExtension $twigExtension,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->dtsmProvider = $dtsmProvider;
        $this->twigExtension = $twigExtension;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param AssetsContainingEvent $event
     * @throws \Exception
     */
    public function beforeAssetsRegistration(AssetsContainingEvent $event)
    {
        $assets = $event->getAssets();
        $templateSubModule = $this->dtsmProvider->getTemplateSubModule();
        $filteredAtRules = [];
        if ($templateSubModule) {
            if ($atRulesContent = $templateSubModule->getContent(BuilderiusTemplateAtRulesContentProvider::CONTENT_TYPE)) {
                $atRulesContent = is_array($atRulesContent) ? $atRulesContent : [];
                foreach($atRulesContent as $atRuleContent) {
                    if (null === $atRuleContent[BuilderiusTemplateAtRulesContentProvider::CONDITION]) {
                        if (!in_array($atRuleContent[BuilderiusTemplateAtRulesContentProvider::URL], $filteredAtRules)) {
                            $filteredAtRules[] = $atRuleContent[BuilderiusTemplateAtRulesContentProvider::URL];
                        }
                    } else {
                        $condResult = $this->twigExtension->evaluateVisibilityCondition($atRuleContent[BuilderiusTemplateAtRulesContentProvider::CONDITION]);
                        if (true === $condResult && !in_array($atRuleContent[BuilderiusTemplateAtRulesContentProvider::URL], $filteredAtRules)) {
                            $filteredAtRules[] = $atRuleContent[BuilderiusTemplateAtRulesContentProvider::URL];
                        }
                    }
                }
            }
        }
        $atRulesEvent = new ConfigContainingEvent($filteredAtRules);
        $this->eventDispatcher->dispatch($atRulesEvent, 'builderius_template_css_at_rules_registration_frontend');
        $this->eventDispatcher->dispatch($atRulesEvent, 'builderius_template_css_at_rules');
        foreach ($atRulesEvent->getConfig() as $atRule) {
            $assets[] = new Style([
                Style::CATEGORY_FIELD => 'frontend',
                Style::HANDLE_FIELD => 'builderius-google-fonts',
                Style::SOURCE_FIELD => $atRule
            ]);
        }

        $event->setAssets($assets);
    }
}