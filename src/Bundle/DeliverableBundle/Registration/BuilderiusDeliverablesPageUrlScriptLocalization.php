<?php

namespace Builderius\Bundle\DeliverableBundle\Registration;

use Builderius\Bundle\BuilderBundle\Registration\AbstractBuilderiusBuilderScriptLocalization;

class BuilderiusDeliverablesPageUrlScriptLocalization extends AbstractBuilderiusBuilderScriptLocalization
{
    const PROPERTY_NAME = 'deliverablesPageUrl';

    /**
     * @inheritDoc
     */
    public function getPropertyData()
    {
        global $_parent_pages;

        if (!isset($_parent_pages['builderius-deliverables'])) {
            $_parent_pages['builderius-deliverables'] = 'builderius-deliverables';
        }

        return menu_page_url('builderius-deliverables', false);
    }
}
