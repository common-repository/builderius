<?php

namespace Builderius\Bundle\TemplateBundle\DynamicDataHelper;

interface DynamicDataHelpersProviderInterface
{
    /**
     * @return DynamicDataHelperInterface[]
     */
    public function getDynamicDataHelpers();

    /**
     * @return DynamicDataHelperInterface|null
     */
    public function getDynamicDataHelper($name);

    /**
     * @return bool
     */
    public function hasDynamicDataHelper($name);
}