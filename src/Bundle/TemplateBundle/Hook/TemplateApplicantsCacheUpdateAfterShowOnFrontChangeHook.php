<?php

namespace Builderius\Bundle\TemplateBundle\Hook;

use Builderius\Bundle\TemplateBundle\Applicant\BuilderiusTemplateApplicantChangeSet;
use Builderius\Bundle\TemplateBundle\Applicant\BuilderiusTemplateApplicantChangeSetInterface;
use Builderius\Bundle\TemplateBundle\Provider\Applicant\BuilderiusTemplateApplicantsProvider;

class TemplateApplicantsCacheUpdateAfterShowOnFrontChangeHook extends AbstractTemplateApplicantsCacheUpdateHook
{
    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        $optionName = func_get_arg(0);
        $oldValue = func_get_arg(1);
        $newValue = func_get_arg(2);
        if ($optionName === 'show_on_front' && $oldValue !== $newValue) {
            $cache = wp_cache_get(BuilderiusTemplateApplicantsProvider::CACHE_KEY);
            if (false !== $cache && is_array($cache)) {
                $pageOnFront = get_option('page_on_front');
                $postsPageId = get_option('page_for_posts');
                if ('page' === $newValue) {
                    $cache = $this->processPage($pageOnFront, $cache);
                    $k = sprintf('singular.single.page.%s', $postsPageId);
                    foreach ($this->getApplyRules() as $hash => $applyRuleConfig) {
                        if (isset($cache[$hash]) && is_array($cache[$hash])) {
                            if (isset($cache[$hash][$k])) {
                                unset($cache[$hash][$k]);
                                uasort($cache[$hash], [AbstractTemplateApplicantsCacheUpdateHook::class, 'sortApplicants']);
                            }
                        }
                    }
                } elseif ('posts' === $newValue) {
                    $cache = $this->processPage($pageOnFront, $cache);
                    $cache = $this->processPage($postsPageId, $cache);
                }
                wp_cache_set(BuilderiusTemplateApplicantsProvider::CACHE_KEY, $cache);
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