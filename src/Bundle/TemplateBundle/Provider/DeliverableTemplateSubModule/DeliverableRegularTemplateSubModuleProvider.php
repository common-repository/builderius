<?php

namespace Builderius\Bundle\TemplateBundle\Provider\DeliverableTemplateSubModule;

use Builderius\Bundle\DeliverableBundle\Model\BuilderiusDeliverableSubModuleInterface;
use Builderius\Bundle\DeliverableBundle\Provider\BuilderiusDeliverableProviderInterface;
use Builderius\Bundle\TemplateBundle\ApplyRule\Checker\BuilderiusTemplateApplyRulesChecker;
use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\Bundle\TemplateBundle\Model\BuilderiusTemplate;

class DeliverableRegularTemplateSubModuleProvider implements DeliverableTemplateSubModuleProviderInterface
{
    /**
     * @var BuilderiusDeliverableProviderInterface
     */
    private $deliverableProvider;

    /**
     * @var BuilderiusRuntimeObjectCache
     */
    protected $cache;

    /**
     * @var BuilderiusTemplateApplyRulesChecker
     */
    private $applyRuleChecker;

    /**
     * @param BuilderiusDeliverableProviderInterface $deliverableProvider
     * @param BuilderiusRuntimeObjectCache $cache
     * @param BuilderiusTemplateApplyRulesChecker $applyRuleChecker
     */
    public function __construct(
        BuilderiusDeliverableProviderInterface $deliverableProvider,
        BuilderiusRuntimeObjectCache $cache,
        BuilderiusTemplateApplyRulesChecker $applyRuleChecker
    ) {
        $this->deliverableProvider = $deliverableProvider;
        $this->cache = $cache;
        $this->applyRuleChecker = $applyRuleChecker;
    }

    /**
     * @inheritDoc
     */
    public function getTemplateSubModule()
    {
        $templateSubModule = $this->cache->get('dtsm');
        if (false === $templateSubModule) {
            $templateSubModule = null;
            $deliverable = $this->deliverableProvider->getDeliverable();
            if ($deliverable) {
                $applicableTmpltSubModules = [];
                $tmpltSubModules = $deliverable->getSubModules('template', 'html', 'regular');
                if (empty($tmpltSubModules)) {
                    $tmpltSubModulesWithOldTypes = $deliverable->getSubModules('template', 'html');
                    foreach ($tmpltSubModulesWithOldTypes as $oldTypeSubModule) {
                        if (in_array($oldTypeSubModule->getType(), ['singular', 'collection', 'other'])) {
                            $tmpltSubModules[] = $oldTypeSubModule;
                        }
                    }
                }
                foreach ($tmpltSubModules as $tmpltSubModule) {
                    $applyRuleConfig = $tmpltSubModule->getAttribute(BuilderiusTemplate::APPLY_RULES_CONFIG_FIELD) ?: [];
                    if ($this->applyRuleChecker->checkApplyRule($applyRuleConfig)) {
                        $applicableTmpltSubModules[] = $tmpltSubModule;
                    }
                }
                if (count($applicableTmpltSubModules) > 1) {
                    usort($applicableTmpltSubModules, function (BuilderiusDeliverableSubModuleInterface $a, BuilderiusDeliverableSubModuleInterface $b) {
                        $aSortOrder = (int)$a->getAttribute(BuilderiusTemplate::SORT_ORDER_FIELD) ?: 10;
                        $bSortOrder = (int)$b->getAttribute(BuilderiusTemplate::SORT_ORDER_FIELD) ?: 10;
                        if ($aSortOrder < $bSortOrder) {
                            return -1;
                        } elseif ($aSortOrder > $bSortOrder) {
                            return 1;
                        } else {
                            return 0;
                        }
                    });
                }
                $templateSubModule = reset($applicableTmpltSubModules);
                $this->cache->set('dtsm', $templateSubModule);
            }
        }

        return $templateSubModule;
    }
}