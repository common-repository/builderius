<?php

namespace Builderius\Bundle\TemplateBundle\Provider\Content\Template;

use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetAwareInterface;

class BuilderiusTemplateModulesWithInlineAssetsContentProvider extends BuilderiusTemplateModulesWithAssetsContentProvider
{
    const CONTENT_TYPE = 'modulesWithInlineAssets';

    const CONTENT_EXTRA_TYPE = 'html';

    /**
     * @param AssetAwareInterface $module
     * @return bool
     */
    protected function checkAssets(AssetAwareInterface $module)
    {
        return !empty($module->getInlineAssets());
    }
}