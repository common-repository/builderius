<?php

namespace Builderius\Bundle\BuilderBundle\Hook;

use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractAction;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\Provider\PluginsVersionsProvider;

class ShowChangelogAdminNoticeHook extends AbstractAction
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
        $dismissedChangelog = get_user_meta($user_id, 'builderius_changelog_notice_dismissed', true);
        if (!$dismissedChangelog) {
            $this->renderChangelogNotice();
        } else {
            if (version_compare($dismissedChangelog, $this->pluginsVersionsProvider->getPluginVersion('builderius')) === -1) {
                $this->renderChangelogNotice();
            }
        }
    }

    private function renderChangelogNotice()
    {
        $url = $_SERVER['REQUEST_URI'];
        if (!strpos($url, '?')) {
            $url = sprintf('%s?builderius-changelog-dismissed', $url);
        } else {
            $url = sprintf('%s&builderius-changelog-dismissed', $url);
        }
        $html = '<div class="notice notice-info is-dismissible">
                    <h2>Builderius 1.0 Alpha has been released (YouTube Video)</h2>
                    <p><a href="https://www.youtube.com/watch?v=-ecvt6xrqCA" target="_blank">Watch the video on YouTube</a></p>
                
                    <h2>Builderius 1.0 Alpha Information</h2>
                    <p>Current stable Builderius version (0.15) is in maintenance mode. We keep it updated, tested, and secure, but we are in the process of redevelopment and redesign of the entire product. Builderius 1.0 Alpha of the upcoming builder has been released in July 2024. You can download your copy on <a href="https://builderius.io/alpha/" target="_blank">https://builderius.io/alpha/</a> until we reach a stable version, at which point we will update the version in the WordPress Plugin Repository. Please don\'t use it on production websites.</p>
                
                    <h2>Compatibility Notice!</h2>
                    <p>New Builderius 1.0 free version is <strong>NOT COMPATIBLE</strong> with the old Pro version. <strong>DO NOT</strong> update your existing website built with the old version of Pro until we have released both Free and the Pro 1.0 stable versions. New versions are a complete overwrite, and mixing New Free and old Pro versions will not work.</p>
                    <a class="notice-dismiss" href="' . $url . '"><span class="screen-reader-text">Dismiss</span></a>
                </div>';

        echo $html;
    }
}