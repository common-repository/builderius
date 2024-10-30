<?php

namespace Builderius\Bundle\TemplateBundle\Hook;

use Builderius\Bundle\TemplateBundle\Applicant\BuilderiusTemplateApplicantChangeSet;
use Builderius\Bundle\TemplateBundle\Applicant\BuilderiusTemplateApplicantChangeSetInterface;
use Builderius\Bundle\TemplateBundle\Provider\Applicant\BuilderiusTemplateApplicantsProvider;

class TemplateApplicantsCacheUpdateAfterPostDeleteHook extends AbstractTemplateApplicantsCacheUpdateHook
{
    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        $post = func_get_arg(1);
        if ($post instanceof \WP_Post) {
            $cache = wp_cache_get(BuilderiusTemplateApplicantsProvider::CACHE_KEY);
            if (false !== $cache && is_array($cache)) {
                $changeset = new BuilderiusTemplateApplicantChangeSet([
                    BuilderiusTemplateApplicantChangeSet::ACTION_FIELD => BuilderiusTemplateApplicantChangeSetInterface::DELETE_ACTION,
                    BuilderiusTemplateApplicantChangeSet::OBJECT_BEFORE_FIELD => $post
                ]);
                foreach ($this->getApplyRules() as $hash => $applyRuleConfig) {
                    if (isset($cache[$hash]) && is_array($cache[$hash])) {
                        $changeSetApplicants = $this->changeSetApplicantsProvider->getChangeSetApplicants($changeset, $applyRuleConfig);
                        foreach ($changeSetApplicants as $k => $applicant) {
                            unset($cache[$hash][$k]);
                        }
                        uasort($cache[$hash], [AbstractTemplateApplicantsCacheUpdateHook::class, 'sortApplicants']);
                    }
                }
                wp_cache_set(BuilderiusTemplateApplicantsProvider::CACHE_KEY, $cache);
            }
        }
    }
}