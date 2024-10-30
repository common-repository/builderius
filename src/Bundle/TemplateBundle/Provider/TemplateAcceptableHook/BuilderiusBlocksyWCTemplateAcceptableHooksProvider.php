<?php

namespace Builderius\Bundle\TemplateBundle\Provider\TemplateAcceptableHook;

use Builderius\Bundle\TemplateBundle\Model\BuilderiusTemplateAcceptableHookInterface;

class BuilderiusBlocksyWCTemplateAcceptableHooksProvider implements BuilderiusTemplateAcceptableHooksProviderInterface
{
    /**
     * @var BuilderiusTemplateAcceptableHookInterface[]
     */
    private $hooks = [];

    /**
     * @param BuilderiusTemplateAcceptableHookInterface $hook
     * @return $this
     */
    public function addHook(BuilderiusTemplateAcceptableHookInterface $hook)
    {
        $this->hooks[] = $hook;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isAcceptable()
    {
        $theme = wp_get_theme();

        if ( 'Blocksy' === $theme->get('Name') || 'blocksy' === $theme->template ) {
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            if ( is_plugin_active( 'woocommerce/woocommerce.php') ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function getAcceptableHooks()
    {
        return $this->hooks;
    }
}