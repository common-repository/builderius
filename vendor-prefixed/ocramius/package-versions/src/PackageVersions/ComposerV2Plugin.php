<?php

declare (strict_types=1);
namespace Builderius\PackageVersions;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
/**
 * @internal do not rely on this: it is only here to declare the composer V2 API explicitly, even
 *           in composer V1 context.
 */
interface ComposerV2Plugin extends \Composer\Plugin\PluginInterface
{
    public function deactivate(\Composer\Composer $composer, \Composer\IO\IOInterface $io) : void;
    public function uninstall(\Composer\Composer $composer, \Composer\IO\IOInterface $io) : void;
}
