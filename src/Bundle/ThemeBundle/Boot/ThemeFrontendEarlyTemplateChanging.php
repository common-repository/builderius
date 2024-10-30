<?php

namespace Builderius\Bundle\ThemeBundle\Boot;

use Builderius\Bundle\TemplateBundle\Cache\BuilderiusPersistentObjectCache;
use Builderius\Bundle\ThemeBundle\Hook\ThemeFrontendLateTemplateChangingHook;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\Boot\BootServiceInterface;
use Builderius\Symfony\Component\Cache\CacheItem;
use Builderius\Symfony\Component\DependencyInjection\ContainerInterface;

class ThemeFrontendEarlyTemplateChanging implements BootServiceInterface
{
    /**
     * @inheritDoc
     */
    public function boot(ContainerInterface $container)
    {
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'GET' &&
            (
                !defined('LOGGED_IN_COOKIE') ||
                !isset($_COOKIE[LOGGED_IN_COOKIE])
            ) &&
            (
                !defined('AUTH_COOKIE') ||
                !isset($_COOKIE[AUTH_COOKIE])
            ) &&
            (
                !defined('BUILDERIUS_USER_SWITCHING_OLDUSER_COOKIE') ||
                !isset($_COOKIE[BUILDERIUS_USER_SWITCHING_OLDUSER_COOKIE])
            )
        ) {
            try {
                /** @var BuilderiusPersistentObjectCache $persistentCache */
                $persistentCache = $container->get('builderius.cache.persistent');
                $fileName = str_replace("/", "", str_replace(":", "", str_replace(".", "", $this->curPageURL())));
                if ('' === $fileName) {
                    $fileName = 'index';
                }
                $encodedCookie = ThemeFrontendLateTemplateChangingHook::encodeCookie();
                if (!empty($encodedCookie)) {
                    $fileName .= '-' . $encodedCookie;
                }
                /** @var CacheItem $cachedItem */
                $cachedItem = $persistentCache->getItem(sprintf('published-%s', $fileName));
                $value = $cachedItem->get();
                if (null !== $value) {
                    echo $value;
                    exit;
                }
            } catch (\Throwable $e) {

            }
        }
    }

    /**
     * @return string
     */
    private function curPageURL() {

        return $_SERVER["REQUEST_URI"];
    }
}