<?php

namespace Builderius\Bundle\BuilderBundle\Hook;

use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractAction;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\Provider\PluginsVersionsProvider;

class DismissChangelogAdminNoticeHook extends AbstractAction
{
    /**
     * @var PluginsVersionsProvider
     */
    private $pluginsVersionsProvider;

    /**
     * @param PluginsVersionsProvider $pluginsVersionsProvider
     */
    public function setPluginsVersionsProvider(PluginsVersionsProvider $pluginsVersionsProvider)
    {
        $this->pluginsVersionsProvider = $pluginsVersionsProvider;
    }

    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        $user_id = get_current_user_id();
        if ( isset( $_GET['builderius-changelog-dismissed'] ) )
            update_user_meta(
                $user_id,
                'builderius_changelog_notice_dismissed',
                $this->pluginsVersionsProvider->getPluginVersion('builderius')
            );
    }
}