<?php

namespace Builderius\Bundle\TemplateBundle\EventListener;

use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\Bundle\TemplateBundle\Event\ConfigContainingEvent;
use Builderius\Bundle\TemplateBundle\Provider\Content\Template\BuilderiusTemplateAtRulesContentProvider;
use Builderius\Bundle\TemplateBundle\Provider\DeliverableTemplateSubModule\DeliverableTemplateSubModulesProviderInterface;
use Builderius\Bundle\TemplateBundle\Twig\TemplateDataVarsExtension;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Event\AssetsContainingEvent;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\Style;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\EventDispatcher\ConditionAwareEventListener;
use Builderius\Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AtRulesRegistrationInHookTemplatesFrontendModeEventListener extends ConditionAwareEventListener
{
    /**
     * @var DeliverableTemplateSubModulesProviderInterface
     */
    private $dhtsmsProvider;

    /**
     * @var TemplateDataVarsExtension
     */
    private $twigExtension;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var BuilderiusRuntimeObjectCache
     */
    private $cache;

    /**
     * @param DeliverableTemplateSubModulesProviderInterface $dhtsmsProvider
     * @param TemplateDataVarsExtension $twigExtension
     * @param EventDispatcherInterface $eventDispatcher
     * @param BuilderiusRuntimeObjectCache $cache
     */
    public function __construct(
        DeliverableTemplateSubModulesProviderInterface $dhtsmsProvider,
        TemplateDataVarsExtension $twigExtension,
        EventDispatcherInterface $eventDispatcher,
        BuilderiusRuntimeObjectCache $cache
    ) {
        $this->dhtsmsProvider = $dhtsmsProvider;
        $this->twigExtension = $twigExtension;
        $this->eventDispatcher = $eventDispatcher;
        $this->cache = $cache;
    }

    /**
     * @param AssetsContainingEvent $event
     * @throws \Exception
     */
    public function beforeAssetsRegistration(AssetsContainingEvent $event)
    {
        $assets = $event->getAssets();
        $templateSubModules = $this->dhtsmsProvider->getTemplateSubModules();
        $filteredAtRules = [];
        foreach ($templateSubModules as $templateSubModule) {
            $this->cache->set('builderius_dtsm_hook_template', $templateSubModule);
            $index = $this->cache->get(sprintf('dtsm_hook_at_rules_registration_%d_index', $templateSubModule->getId()));
            if (false === $index) {
                $index = 0;
            } else {
                $index = $index + 1;
            }
            $cachedHookArgs = $this->cache->get(sprintf('dtsm_hook_template_args_%d_%d', $templateSubModule->getId(), $index));
            $this->cache->set(sprintf('dtsm_hook_at_rules_registration_%d_index', $templateSubModule->getId()), $index);
            if (false !== $cachedHookArgs) {
                $this->cache->set('hook_template_args', $cachedHookArgs);
            }
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
            $this->cache->delete('builderius_dtsm_hook_template');
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