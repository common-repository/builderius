<?php

namespace Builderius\MooMoo\Platform\Bundle\AssetBundle\Registrator\InlineAssets\Chain\Element;

class AdminScriptInlineAssetsRegistratorChainElement extends \Builderius\MooMoo\Platform\Bundle\AssetBundle\Registrator\InlineAssets\Chain\Element\FrontendScriptInlineAssetsRegistratorChainElement
{
    /**
     * @var string
     */
    protected $assetRegistrationFunction = 'admin_footer';
    /**
     * @var string
     */
    protected $registrationFunction = 'admin_enqueue_scripts';
}
