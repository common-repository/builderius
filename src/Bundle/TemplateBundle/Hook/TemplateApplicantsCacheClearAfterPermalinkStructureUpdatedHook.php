<?php

namespace Builderius\Bundle\TemplateBundle\Hook;

use Builderius\Bundle\TemplateBundle\Provider\Applicant\BuilderiusTemplateApplicantsProvider;
use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractAction;

class TemplateApplicantsCacheClearAfterPermalinkStructureUpdatedHook extends AbstractAction
{
    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        $optionName = func_get_arg(0);
        $oldValue = func_get_arg(1);
        $newValue = func_get_arg(2);
        if ($optionName === 'permalink_structure' && $oldValue !== $newValue) {
            $cache = wp_cache_get(BuilderiusTemplateApplicantsProvider::CACHE_KEY);
            if (false !== $cache && is_array($cache)) {
                wp_cache_delete(BuilderiusTemplateApplicantsProvider::CACHE_KEY);
            }
        }
    }
}