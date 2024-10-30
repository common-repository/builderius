<?php

namespace Builderius\Bundle\TemplateBundle\Provider\DeliverableTemplateSubModule;

use Builderius\Bundle\DeliverableBundle\Model\BuilderiusDeliverableSubModuleInterface;
use Builderius\Bundle\DeliverableBundle\Provider\BuilderiusDeliverableProviderInterface;
use Builderius\Bundle\TemplateBundle\ApplyRule\Checker\BuilderiusTemplateApplyRulesChecker;
use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\Bundle\TemplateBundle\Model\BuilderiusTemplate;

class DeliverableHookTemplateSubModulesProvider implements DeliverableTemplateSubModulesProviderInterface
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
    public function getTemplateSubModules()
    {
        $applicableTmpltSubModules = $this->cache->get('hook_dtsms');
        if (false === $applicableTmpltSubModules) {
            $applicableTmpltSubModules = [];
            $deliverable = $this->deliverableProvider->getDeliverable();
            if ($deliverable) {
                $tmpltSubModules = $deliverable->getSubModules('template', 'html', 'hook');
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
                $this->cache->set('hook_dtsms', $applicableTmpltSubModules);
            }
        }

        return $applicableTmpltSubModules;
    }
}