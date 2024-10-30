<?php

namespace Builderius\Bundle\SettingBundle\Converter\Setting\Migration;

interface BuilderiusSettingMigrationConverterInterface
{
    /**
     * @return string
     */
    public function getSourceVersion();

    /**
     * @return string
     */
    public function getTargetVersion();

    /**
     * @return string
     */
    public function getSourceName();

    /**
     * @return string
     */
    public function getTargetName();

    /**
     * @param array $config
     * @return array
     */
    public function convert(array $config);
}