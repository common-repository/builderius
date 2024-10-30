<?php

namespace Builderius\Bundle\TemplateBundle\Helper;

class ContentConfigHelper
{
    /**
     * @param array $config
     * @return array
     */
    public static function formatConfig(array $config = null)
    {
        if ($config && !empty($config)) {
            foreach ($config as $key => $value) {
                if (is_array($value) && empty($value)) {
                    $config[$key] = new \StdClass();
                }
            }
        }

        return $config;
    }
}