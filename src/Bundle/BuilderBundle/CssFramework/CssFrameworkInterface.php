<?php

namespace Builderius\Bundle\BuilderBundle\CssFramework;

interface CssFrameworkInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return array
     */
    public function getClasses();

    /**
     * @return array
     */
    public function getVariables();
}