<?php

namespace Builderius\MooMoo\Platform\Bundle\OptionBundle\Registrator\Chain\Element;

use Builderius\MooMoo\Platform\Bundle\OptionBundle\Model\OptionInterface;
interface OptionsRegistratorChainElementInterface
{
    /**
     * @param OptionInterface $option
     * @return bool
     */
    public function isApplicable(\Builderius\MooMoo\Platform\Bundle\OptionBundle\Model\OptionInterface $option);
    /**
     * @param OptionInterface $option
     */
    public function register(\Builderius\MooMoo\Platform\Bundle\OptionBundle\Model\OptionInterface $option);
}
