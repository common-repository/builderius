<?php

namespace Builderius\Bundle\BuilderBundle\Registration;

class BuilderiusBuilderAjaxScriptLocalization extends AbstractBuilderiusBuilderScriptLocalization
{
    const PROPERTY_NAME = 'ajax';

    /**
     * @inheritDoc
     */
    public function getPropertyData()
    {
        return [
            'ajax_url' => admin_url('admin-ajax.php'),
            'ajax_nonce' => wp_create_nonce('builderius_ajax_nonce'),
        ];
    }
}
