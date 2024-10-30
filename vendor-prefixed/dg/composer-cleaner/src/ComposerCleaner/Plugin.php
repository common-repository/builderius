<?php

declare (strict_types=1);
namespace Builderius\DG\ComposerCleaner;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use Composer\Util\Filesystem;
use Composer\Util\ProcessExecutor;
class Plugin implements \Composer\Plugin\PluginInterface, \Composer\EventDispatcher\EventSubscriberInterface
{
    public function activate(\Composer\Composer $composer, \Composer\IO\IOInterface $io)
    {
    }
    public function deactivate(\Composer\Composer $composer, \Composer\IO\IOInterface $io)
    {
    }
    public function uninstall(\Composer\Composer $composer, \Composer\IO\IOInterface $io)
    {
    }
    public static function getSubscribedEvents()
    {
        return [\Composer\Script\ScriptEvents::POST_UPDATE_CMD => 'clean', \Composer\Script\ScriptEvents::POST_INSTALL_CMD => 'clean'];
    }
    public function clean(\Composer\Script\Event $event)
    {
        $vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');
        $extra = $event->getComposer()->getPackage()->getExtra();
        $ignorePaths = $extra['cleaner-ignore'] ?? (array) $event->getComposer()->getConfig()->get('cleaner-ignore');
        $fileSystem = new \Composer\Util\Filesystem(new \Composer\Util\ProcessExecutor($event->getIO()));
        $cleaner = new \Builderius\DG\ComposerCleaner\Cleaner($event->getIO(), $fileSystem);
        $cleaner->clean($vendorDir, $ignorePaths);
    }
}
