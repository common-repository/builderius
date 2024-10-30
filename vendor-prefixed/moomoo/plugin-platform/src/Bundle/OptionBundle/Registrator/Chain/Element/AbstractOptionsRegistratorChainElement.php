<?php

namespace Builderius\MooMoo\Platform\Bundle\OptionBundle\Registrator\Chain\Element;

use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareInterface;
use Builderius\MooMoo\Platform\Bundle\OptionBundle\Model\OptionInterface;
use Builderius\MooMoo\Platform\Bundle\OptionBundle\Registrator\OptionsRegistratorInterface;
abstract class AbstractOptionsRegistratorChainElement implements \Builderius\MooMoo\Platform\Bundle\OptionBundle\Registrator\OptionsRegistratorInterface, \Builderius\MooMoo\Platform\Bundle\OptionBundle\Registrator\Chain\Element\OptionsRegistratorChainElementInterface
{
    /**
     * @var OptionInterface[]
     */
    private $options = [];
    /**
     * @param OptionInterface $option
     * @return $this
     */
    public function addOption(\Builderius\MooMoo\Platform\Bundle\OptionBundle\Model\OptionInterface $option)
    {
        $this->options[$option->getName()] = $option;
        return $this;
    }
    /**
     * @var OptionsRegistratorChainElementInterface|null
     */
    private $successor;
    /**
     * @inheritDoc
     */
    public function registerOptions()
    {
        $options = $this->options;
        add_action('init', function () use($options) {
            /** @var OptionInterface $option */
            foreach ($options as $option) {
                if ($option instanceof \Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareInterface && $option->hasConditions()) {
                    $evaluated = \true;
                    foreach ($option->getConditions() as $condition) {
                        if ($condition->evaluate() === \false) {
                            $evaluated = \false;
                            break;
                        }
                    }
                    if (!$evaluated) {
                        continue;
                    }
                    $this->registerOption($option);
                } else {
                    $this->registerOption($option);
                }
            }
        });
    }
    /**
     * @param OptionInterface $option
     */
    private function registerOption(\Builderius\MooMoo\Platform\Bundle\OptionBundle\Model\OptionInterface $option)
    {
        if ($this->isApplicable($option)) {
            $this->register($option);
        } elseif ($this->getSuccessor() && $this->getSuccessor()->isApplicable($option)) {
            $this->getSuccessor()->register($option);
        }
    }
    /**
     * @param OptionsRegistratorChainElementInterface $optionRegistrator
     */
    public function setSuccessor(\Builderius\MooMoo\Platform\Bundle\OptionBundle\Registrator\Chain\Element\OptionsRegistratorChainElementInterface $optionRegistrator)
    {
        $this->successor = $optionRegistrator;
    }
    /**
     * @return OptionsRegistratorChainElementInterface|null
     */
    protected function getSuccessor()
    {
        return $this->successor;
    }
}
