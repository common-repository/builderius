<?php

namespace Builderius\MooMoo\Platform\Bundle\AssetBundle\Registrator\Assets\Chain\Element;

use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetInterface;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\ScriptInterface;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\ScriptLocalizationInterface;
use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareInterface;
class ScriptAssetsRegistratorChainElement extends \Builderius\MooMoo\Platform\Bundle\AssetBundle\Registrator\Assets\Chain\Element\AbstractAssetsRegistratorChainElement
{
    /**
     * @inheritDoc
     */
    public function isApplicable(\Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetInterface $asset)
    {
        return $asset instanceof \Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\ScriptInterface;
    }
    /**
     * @inheritDoc
     */
    public function register(\Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetInterface $asset)
    {
        /** @var ScriptInterface $asset */
        if ($asset->registerOnly()) {
            wp_register_script($asset->getHandle(), $this->pathProvider->getAssetPath($asset), $asset->getDependencies(), $asset->getVersion(), $asset->isInFooter() ?: \false);
        } else {
            wp_enqueue_script($asset->getHandle(), $this->pathProvider->getAssetPath($asset), $asset->getDependencies(), $asset->getVersion(), $asset->isInFooter() ?: \false);
        }
        if (!empty($asset->getLocalizations())) {
            $params = $this->transformLocalizations($asset->getLocalizations());
            foreach ($params as $objectName => $data) {
                wp_localize_script($asset->getHandle(), $objectName, $data);
            }
        }
        if (!empty($asset->getAssetData())) {
            $groupedData = [];
            foreach ($asset->getAssetData() as $dataItem) {
                $groupedData[$dataItem->getGroup()][$dataItem->getKey()] = $dataItem->getValue();
            }
            foreach ($groupedData as $group => $values) {
                wp_script_add_data($asset->getHandle(), $group, $values);
            }
        }
    }
    /**
     * @param ScriptLocalizationInterface[] $localizations
     * @return array
     */
    private function transformLocalizations(array $localizations)
    {
        $params = [];
        /** @var ScriptLocalizationInterface $localization */
        foreach ($localizations as $localization) {
            if ($localization instanceof \Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareInterface && $localization->hasConditions()) {
                $evaluated = \true;
                foreach ($localization->getConditions() as $condition) {
                    if ($condition->evaluate() === \false) {
                        $evaluated = \false;
                        break;
                    }
                }
                if (!$evaluated) {
                    continue;
                }
                $params[$localization->getObjectName()][$localization->getPropertyName()] = $localization->getPropertyData();
            } else {
                $params[$localization->getObjectName()][$localization->getPropertyName()] = $localization->getPropertyData();
            }
        }
        return $params;
    }
}
