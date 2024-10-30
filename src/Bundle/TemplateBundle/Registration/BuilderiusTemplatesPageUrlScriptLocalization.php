<?php

namespace Builderius\Bundle\TemplateBundle\Registration;

use Builderius\Bundle\BuilderBundle\Registration\AbstractBuilderiusBuilderScriptLocalization;

class BuilderiusTemplatesPageUrlScriptLocalization extends AbstractBuilderiusBuilderScriptLocalization
{
    const PROPERTY_NAME = 'templatesPageUrl';

    /**
     * @inheritDoc
     */
    public function getPropertyData()
    {
        global $_parent_pages;

        if (!isset($_parent_pages['builderius-templates'])) {
            $_parent_pages['builderius-templates'] = 'builderius-templates';
        }

        return menu_page_url('builderius-templates', false);
    }
}
