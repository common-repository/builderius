<?php

namespace Builderius\MooMoo\Platform\Bundle\AssetBundle\Model;

class Script extends \Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AbstractAsset implements \Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\ScriptInterface
{
    const IN_FOOTER_FIELD = 'inFooter';
    const LOCALIZATIONS = 'localizations';
    /**
     * @inheritDoc
     */
    public function isInFooter()
    {
        return $this->get(self::IN_FOOTER_FIELD, \false);
    }
    /**
     * @inheritDoc
     */
    public function getLocalizations()
    {
        $localizations = $this->get(self::LOCALIZATIONS, []);
        usort($localizations, function (\Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\ScriptLocalizationInterface $a, \Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\ScriptLocalizationInterface $b) {
            if ($a->getSortOrder() < $b->getSortOrder()) {
                return -1;
            } elseif ($a->getSortOrder() > $b->getSortOrder()) {
                return 1;
            } else {
                return 0;
            }
        });

        return $localizations;
    }
    /**
     * @inheritDoc
     */
    public function addLocalization(\Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\ScriptLocalizationInterface $localization)
    {
        $localizations = $this->getLocalizations();
        if (!\in_array($localization, $localizations)) {
            $localizations[] = $localization;
            $this->set(self::LOCALIZATIONS, $localizations);
        }
        return $this;
    }
}
