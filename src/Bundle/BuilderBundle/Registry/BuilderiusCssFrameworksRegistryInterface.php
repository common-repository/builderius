<?php

namespace Builderius\Bundle\BuilderBundle\Registry;

use Builderius\Bundle\BuilderBundle\CssFramework\CssFrameworkInterface;

interface BuilderiusCssFrameworksRegistryInterface
{
    /**
     * @return CssFrameworkInterface[]
     */
    public function getFrameworks();

    /**
     * @param string $name
     * @return CssFrameworkInterface
     */
    public function getFramework($name);

    /**
     * @param string $name
     * @return bool
     */
    public function hasFramework($name);
}