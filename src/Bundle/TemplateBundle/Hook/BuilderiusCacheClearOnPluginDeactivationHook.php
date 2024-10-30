<?php

namespace Builderius\Bundle\TemplateBundle\Hook;

use Builderius\Bundle\TemplateBundle\ApplyRule\Provider\AvailablePagesProvider;
use Builderius\Bundle\TemplateBundle\ApplyRule\Provider\AvailablePostsProvider;
use Builderius\Bundle\TemplateBundle\Provider\Applicant\BuilderiusTemplateApplicantsProvider;
use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractAction;

class BuilderiusCacheClearOnPluginDeactivationHook extends AbstractAction
{
    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        wp_cache_delete(BuilderiusTemplateApplicantsProvider::CACHE_KEY);
        wp_cache_delete(AvailablePagesProvider::CACHE_KEY);
        wp_cache_delete(AvailablePostsProvider::CACHE_KEY);
    }
}