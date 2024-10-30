<?php

namespace Builderius\Bundle\ThemeBundle\Hook;

use Builderius\Bundle\TemplateBundle\Cache\BuilderiusPersistentObjectCache;
use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractFilter;
use Builderius\Symfony\Component\Cache\CacheItem;
use Builderius\Symfony\Component\Templating\EngineInterface;

class ThemeFrontendLateTemplateChangingHook extends AbstractFilter
{
    /**
     * @var string
     */
    private $templatePath;

    /**
     * @var EngineInterface
     */
    private $templatingEngine;

    /**
     * @var BuilderiusPersistentObjectCache
     */
    private $persistentCache;

    /**
     * @param string $templatePath
     * @return $this
     */
    public function setTemplatePath(string $templatePath)
    {
        $this->templatePath = $templatePath;

        return $this;
    }

    /**
     * @param EngineInterface $templatingEngine
     * @return $this
     */
    public function setTemplatingEngine(EngineInterface $templatingEngine)
    {
        $this->templatingEngine = $templatingEngine;

        return $this;
    }

    /**
     * @param BuilderiusPersistentObjectCache $persistentCache
     * @return $this
     */
    public function setPersistentCache(BuilderiusPersistentObjectCache $persistentCache)
    {
        $this->persistentCache = $persistentCache;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        $html = $this->templatingEngine->render($this->templatePath);
        if (!is_user_logged_in() && $_SERVER['REQUEST_METHOD'] === 'GET') {
            $fileName = str_replace("/", "", str_replace(":", "", str_replace(".", "", $this->curPageURL())));
            if('' === $fileName) {
                $fileName = 'index';
            }
            $encodedCookie = $this->encodeCookie();
            if (!empty($encodedCookie)) {
                $fileName .= '-' . $encodedCookie;
            }
            try {
                /** @var CacheItem $cachedItem */
                $cachedItem = $this->persistentCache->getItem(sprintf('published-%s', $fileName));
                $cachedItem->set($html);
                $cachedItem->expiresAfter(\DateInterval::createFromDateString('10 hours'));

                $this->persistentCache->save($cachedItem);
            } catch (\Throwable $e) {
                $test = $e;
            }
        }

        echo $html;
        exit;
    }

    /**
     * @return string
     */
    private function curPageURL()
    {
        return $_SERVER["REQUEST_URI"];
    }

    /**
     * @return string
     */
    public static function encodeCookie ()
    {
        $excludedKeys = ['XDEBUG_SESSION', 'wordpress_test_cookie', 'wp-settings', '_ga'];
        if (!empty($_COOKIE)) {
            $clone = [];
            foreach ($_COOKIE as $k => $v) {
                $exclude = false;
                foreach ($excludedKeys as $excl) {
                    $pos = strpos(strtolower($k), strtolower($excl));
                    if (strpos(strtolower($k), strtolower($excl)) == 0) {
                        $exclude = true;
                        break;
                    }
                }
                if (false === $exclude) {
                    $clone[$k] = $v;
                }
            }
            return md5(serialize($clone));
        }

        return '';
    }
}
