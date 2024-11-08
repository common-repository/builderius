<?php

namespace Builderius\MooMoo\Platform\Bundle\AssetBundle\DependencyInjection\CompilerPass;

use InvalidArgumentException;
use Builderius\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Builderius\Symfony\Component\DependencyInjection\ContainerBuilder;
use Builderius\Symfony\Component\DependencyInjection\Reference;
class ScriptLocalizationsCompilerPass implements \Builderius\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface
{
    const LOCALIZATION_TAG = 'moomoo_script_localization';
    const ASSET_TAG = 'moomoo_asset';
    /**
     * @var array
     */
    private $assets;
    /**
     * @inheritDoc
     */
    public function process(\Builderius\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        $assets = $container->findTaggedServiceIds(self::ASSET_TAG);
        if (!$assets) {
            return;
        }
        foreach ($assets as $asset => $attributes) {
            $definition = $container->getDefinition($asset);
            $class = $definition->getClass();
            if (\strpos($class, 'Script') !== \false && $definition->getArguments()) {
                $arguments = $definition->getArgument(0);
                $handle = null;
                if (isset($arguments['handle'])) {
                    $handle = $arguments['handle'];
                }
                if ($handle === null) {
                    throw new \InvalidArgumentException(\sprintf('Service "%s" does not have required param "handle"', $asset));
                }
                $this->assets[$handle][] = $asset;
            }
        }
        $localizations = $container->findTaggedServiceIds(self::LOCALIZATION_TAG);
        if ($localizations === null) {
            return;
        }
        foreach ($localizations as $localization => $attributes) {
            $handles = [];
            foreach ($attributes as $attribute) {
                if (isset($attribute['handle'])) {
                    $handles[] = $attribute['handle'];
                }
            }
            if (empty($handles)) {
                throw new \InvalidArgumentException(\sprintf('Tag "%s" for service "%s" does not have required param "handle"', self::LOCALIZATION_TAG, $localization));
            }
            foreach ($handles as $handle) {
                if (isset($this->assets[$handle])) {
                    foreach ($this->assets[$handle] as $k => $asset) {
                        $assetDefinition = $container->getDefinition($this->assets[$handle][$k]);
                        $assetDefinition->addMethodCall('addLocalization', [new \Builderius\Symfony\Component\DependencyInjection\Reference($localization)]);
                    }
                }
            }
        }
    }
}
