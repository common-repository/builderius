<?php

namespace Builderius\Bundle\TemplateBundle\Hook;

use Builderius\Bundle\TemplateBundle\Event\AssetRemovalEvent;
use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractAction;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\EventDispatcher\EventDispatcher;

class RemoveUnnecessaryScriptsAndStylesHook extends AbstractAction
{
    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @param EventDispatcher $eventDispatcher
     * @return $this
     */
    public function setEventDispatcher(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        if (isset($_POST['builderius-applicant-data']) && isset($_POST['disable_theme']) && $_POST['disable_theme'] === "false") {
            return;
        }
        $event = new AssetRemovalEvent('style', 'global-styles', true);
        $this->eventDispatcher->dispatch($event, 'asset_removal');
        if (true === $event->getResult()) {
            wp_dequeue_style( 'global-styles' );
            remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
            remove_action( 'wp_footer', 'wp_enqueue_global_styles', 1 );
        }
        $event = new AssetRemovalEvent('style', 'classic-theme-styles', true);
        $this->eventDispatcher->dispatch($event, 'asset_removal');
        if (true === $event->getResult()) {
            wp_dequeue_style( 'classic-theme-styles' );
            remove_filter( 'block_editor_settings_all', 'wp_add_editor_classic_theme_styles' );
        }
        $event = new AssetRemovalEvent('script', 'wp-emoji-release', true);
        $this->eventDispatcher->dispatch($event, 'asset_removal');
        if (true === $event->getResult()) {
            wp_deregister_script('wp-emoji-release');
            remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
            remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
            remove_action( 'wp_print_styles', 'print_emoji_styles' );
            remove_action( 'admin_print_styles', 'print_emoji_styles' );
            remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
            remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
            remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
        }
        $event = new AssetRemovalEvent('script', 'wp-embed', true);
        $this->eventDispatcher->dispatch($event, 'asset_removal');
        if (true === $event->getResult()) {
            wp_deregister_script('wp-embed');
        }
        $event = new AssetRemovalEvent('style', 'wp-block-library', true);
        $this->eventDispatcher->dispatch($event, 'asset_removal');
        if (true === $event->getResult()) {
            wp_dequeue_style( 'wp-block-library' );
        }
        $event = new AssetRemovalEvent('style', 'wp-block-library-theme', true);
        $this->eventDispatcher->dispatch($event, 'asset_removal');
        if (true === $event->getResult()) {
            wp_dequeue_style( 'wp-block-library-theme' );
        }
    }
}