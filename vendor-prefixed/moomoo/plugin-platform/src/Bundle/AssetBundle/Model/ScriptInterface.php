<?php

namespace Builderius\MooMoo\Platform\Bundle\AssetBundle\Model;

interface ScriptInterface extends \Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetInterface
{
    /**
     * @return bool|null
     */
    public function isInFooter();
    /**
     * @return ScriptLocalizationInterface[]
     */
    public function getLocalizations();
    /**
     * @param ScriptLocalizationInterface $localization
     * @return $this
     */
    public function addLocalization(\Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\ScriptLocalizationInterface $localization);
}
