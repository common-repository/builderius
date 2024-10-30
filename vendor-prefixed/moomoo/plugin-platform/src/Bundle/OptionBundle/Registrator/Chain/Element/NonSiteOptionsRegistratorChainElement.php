<?php

namespace Builderius\MooMoo\Platform\Bundle\OptionBundle\Registrator\Chain\Element;

use Builderius\MooMoo\Platform\Bundle\OptionBundle\Model\NonSiteOptionInterface;
use Builderius\MooMoo\Platform\Bundle\OptionBundle\Model\OptionInterface;
class NonSiteOptionsRegistratorChainElement extends \Builderius\MooMoo\Platform\Bundle\OptionBundle\Registrator\Chain\Element\AbstractOptionsRegistratorChainElement
{
    /**
     * @inheritDoc
     */
    public function isApplicable(\Builderius\MooMoo\Platform\Bundle\OptionBundle\Model\OptionInterface $option)
    {
        return $option instanceof \Builderius\MooMoo\Platform\Bundle\OptionBundle\Model\NonSiteOptionInterface;
    }
    /**
     * @inheritDoc
     */
    public function register(\Builderius\MooMoo\Platform\Bundle\OptionBundle\Model\OptionInterface $option)
    {
        /** @var NonSiteOptionInterface $option */
        add_option($option->getName(), $option->getValue(), $option->getDeprecated() ?: '', $option->isAutoload() ?: 'yes');
    }
}
