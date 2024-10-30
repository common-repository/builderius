<?php

namespace Builderius\MooMoo\Platform\Bundle\AssetBundle\Registrator\InlineAssets\Chain\Element;

class AdminStyleInlineAssetsRegistratorChainElement extends \Builderius\MooMoo\Platform\Bundle\AssetBundle\Registrator\InlineAssets\Chain\Element\FrontendStyleInlineAssetsRegistratorChainElement
{
    /**
     * @var string
     */
    protected $assetRegistrationFunction = 'admin_head';
    /**
     * @var string
     */
    protected $registrationFunction = 'admin_enqueue_scripts';
}
