<?php

namespace Builderius\Bundle\ModuleBundle\Checker\Chain\Element;

use Builderius\Bundle\ModuleBundle\Checker\BuilderiusModuleCheckerInterface;
use Builderius\Bundle\ModuleBundle\Model\BuilderiusModuleInterface;

abstract class AbstractBuilderiusModuleCheckerChainElement implements BuilderiusModuleCheckerInterface
{
    /**
     * @var BuilderiusModuleCheckerInterface|null
     */
    protected $successor;

    /**
     * @param BuilderiusModuleCheckerInterface $checker
     */
    public function setSuccessor(BuilderiusModuleCheckerInterface $checker)
    {
        $this->successor = $checker;
    }

    /**
     * @return BuilderiusModuleCheckerInterface|null
     */
    protected function getSuccessor()
    {
        return $this->successor;
    }

    /**
     * @inheritDoc
     */
    public function check(BuilderiusModuleInterface $module)
    {
        $result = $this->checkModule($module);
        
        if ($this->getSuccessor()) {
            return $this->getSuccessor()->check($module);
        } else {
            return $result;
        }
    }

    /**
     * @param BuilderiusModuleInterface $module
     * @return bool
     */
    abstract protected function checkModule(BuilderiusModuleInterface $module);
}
