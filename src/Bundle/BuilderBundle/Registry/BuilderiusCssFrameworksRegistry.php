<?php

namespace Builderius\Bundle\BuilderBundle\Registry;

use Builderius\Bundle\BuilderBundle\CssFramework\CssFrameworkInterface;

class BuilderiusCssFrameworksRegistry implements BuilderiusCssFrameworksRegistryInterface
{
    /**
     * @var CssFrameworkInterface[]
     */
    protected $frameworks = [];

    /**
     * @param CssFrameworkInterface $framework
     */
    public function addFramework(CssFrameworkInterface $framework)
    {
        $this->frameworks[$framework->getName()] = $framework;
    }

    /**
     * {@inheritdoc}
     */
    public function getFrameworks()
    {
        return $this->frameworks;
    }

    /**
     * {@inheritdoc}
     */
    public function getFramework($name)
    {
        if ($this->hasFramework($name)) {
            return $this->frameworks[$name];
        }
        
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function hasFramework($name)
    {
        return isset($this->frameworks[$name]);
    }
}
