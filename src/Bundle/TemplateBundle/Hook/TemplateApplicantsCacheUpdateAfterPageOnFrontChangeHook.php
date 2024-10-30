<?php

namespace Builderius\Bundle\TemplateBundle\Hook;

use Builderius\Bundle\TemplateBundle\Applicant\BuilderiusTemplateApplicantChangeSet;
use Builderius\Bundle\TemplateBundle\Applicant\BuilderiusTemplateApplicantChangeSetInterface;
use Builderius\Bundle\TemplateBundle\Provider\Applicant\BuilderiusTemplateApplicantsProvider;

class TemplateApplicantsCacheUpdateAfterPageOnFrontChangeHook extends AbstractTemplateApplicantsCacheUpdateHook
{
    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        $optionName = func_get_arg(0);
        $oldValue = func_get_arg(1);
        $newValue = func_get_arg(2);
        if ($optionName === 'page_on_front' && $oldValue !== $newValue) {
            $cache = wp_cache_get(BuilderiusTemplateApplicantsProvider::CACHE_KEY);
            if (false !== $cache && is_array($cache)) {
                if ('page' === get_option('show_on_front')) {
                    $cache = $this->processPage($oldValue, $cache);
                    $cache = $this->processPage($newValue, $cache);
                    wp_cache_set(BuilderiusTemplateApplicantsProvider::CACHE_KEY, $cache);
                }
            }
        }
    }

    /**
     * @param int $id
     * @param array $cache
     * @return array
     */
    private function processPage($id, array $cache)
    {
        $pages = $this->wpQuery->query([
            'lang' => '',
            'posts_per_page' => -1,
            'no_found_rows' => true,
            'post_type' => ['page'],
            'page_id' => $id,
            'post_status' => get_post_stati(),
        ]);
        if (!empty($pages)) {
            $page = reset($pages);
            $changeset = new BuilderiusTemplateApplicantChangeSet([
                BuilderiusTemplateApplicantChangeSet::ACTION_FIELD => BuilderiusTemplateApplicantChangeSetInterface::UPDATE_ACTION,
                BuilderiusTemplateApplicantChangeSet::OBJECT_AFTER_FIELD => $page
            ]);
            foreach ($this->getApplyRules() as $hash => $applyRuleConfig) {
                if (isset($cache[$hash]) && is_array($cache[$hash])) {
                    $changeSetApplicants = $this->changeSetApplicantsProvider->getChangeSetApplicants($changeset, $applyRuleConfig);
                    foreach ($changeSetApplicants as $k => $applicant) {
                        $cache[$hash][$k] = $applicant;
                    }
                    uasort($cache[$hash], [AbstractTemplateApplicantsCacheUpdateHook::class, 'sortApplicants']);
                }
            }
        }

        return $cache;
    }
}