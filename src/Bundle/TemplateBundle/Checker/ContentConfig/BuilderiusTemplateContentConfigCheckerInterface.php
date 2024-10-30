<?php

namespace Builderius\Bundle\TemplateBundle\Checker\ContentConfig;

interface BuilderiusTemplateContentConfigCheckerInterface
{
    /**
     * @param array $contentConfig
     * @return boolean
     * @throws \Exception
     */
    public function check(array $contentConfig);
}
