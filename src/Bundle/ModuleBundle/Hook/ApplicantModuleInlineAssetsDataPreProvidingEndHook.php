<?php

namespace Builderius\Bundle\ModuleBundle\Hook;

use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\Bundle\TemplateBundle\Hook\AbstractApplicantDataAction;

class ApplicantModuleInlineAssetsDataPreProvidingEndHook extends AbstractApplicantDataAction
{
    const CACHE_KEY = 'applicant_inline_assets';

    /**
     * @var string
     */
    private $location = 'header';

    /**
     * @var string
     */
    private $postParameter;

    /**
     * @var BuilderiusRuntimeObjectCache
     */
    private $cache;

    /**
     * @param string $location
     * @return $this
     */
    public function setLocation(string $location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @param string $postParameter
     * @return $this
     */
    public function setPostParameter(string $postParameter)
    {
        $this->postParameter = $postParameter;

        return $this;
    }

    /**
     * @param BuilderiusRuntimeObjectCache $cache
     * @return $this
     */
    public function setCache(BuilderiusRuntimeObjectCache $cache)
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        $user = apply_filters('builderius_get_current_user', $this->getUser());
        if (isset( $_POST[$this->postParameter]) && user_can($user, 'builderius-development')) {
            $content = ob_get_clean();
            preg_match_all('/<style(.*?)<\/style>/s', $content, $styles);
            preg_match_all('/<script(.*?)<\/script>/s', $content, $scripts);
            $existingCache = $this->cache->get(self::CACHE_KEY);
            if (false === $existingCache) {
                $existingCache = [
                    'styles' => [],
                    'scripts' => []
                ];
            }
            if (is_array($styles) && isset($styles[1])) {
                foreach ($styles[1] as $style) {
                    $delimPos = strpos($style, '>');
                    $attrStr = substr($style, 0, $delimPos);
                    $val = substr($style, $delimPos + 1);
                    if (
                        $val !== '' /*&&
                        strpos($attrStr, '-inline-css') === false*/
                    ) {
                        $existingCache['styles'][$this->location][] = [
                            'attributes' => $this->getAttributes($attrStr),
                            'content' => $val
                        ];
                    }
                }
            }
            if (is_array($scripts) && isset($scripts[1])) {
                foreach ($scripts[1] as $script) {
                    $delimPos = strpos($script, '>');
                    $attrStr = substr($script, 0, $delimPos);
                    $val = substr($script, $delimPos + 1);
                    if (
                        $val !== '' &&
                        strpos($attrStr, '-js-extra') === false &&
                        strpos($attrStr, '-js-before') === false &&
                        strpos($attrStr, '-js-after') === false
                    ) {
                        $existingCache['scripts'][$this->location][] = [
                            'attributes' => $this->getAttributes($attrStr),
                            'content' => $val
                        ];
                    }
                }
            }
            $this->cache->set(self::CACHE_KEY, $existingCache);
        }
    }

    /**
     * @param string $attrStr
     * @return array
     */
    private function getAttributes($attrStr)
    {
        $attributes = [];
        if (trim($attrStr) === '') {
            return $attributes;
        }
        preg_match_all('/((?:(?!\\s|=).)*)\\s*?=\\s*?[\\"\']?((?:(?<=\\")(?:(?<=\\\\)\\"|[^\\"])*|(?<=\')(?:(?<=\\\\)\'|[^\'])*)|(?:(?!\\"|\')(?:(?!\\/>|>|\\s).)+))/s', $attrStr, $matched);
        if (isset($matched[1]) && isset($matched[2]) && !empty($matched[1]) && !empty($matched[2]) && count($matched[1]) === count($matched[2]))
        foreach ($matched[1] as $k => $v) {
            $attributes[$v] = $matched[2][$k];
        }

        return $attributes;
    }
}