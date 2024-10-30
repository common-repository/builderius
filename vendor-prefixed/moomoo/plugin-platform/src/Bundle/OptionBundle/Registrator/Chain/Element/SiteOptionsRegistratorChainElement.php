<?php

namespace Builderius\MooMoo\Platform\Bundle\OptionBundle\Registrator\Chain\Element;

use Builderius\MooMoo\Platform\Bundle\OptionBundle\Model\OptionInterface;
use Builderius\MooMoo\Platform\Bundle\OptionBundle\Model\SiteOptionInterface;
class SiteOptionsRegistratorChainElement extends \Builderius\MooMoo\Platform\Bundle\OptionBundle\Registrator\Chain\Element\AbstractOptionsRegistratorChainElement
{
    /**
     * @inheritDoc
     */
    public function isApplicable(\Builderius\MooMoo\Platform\Bundle\OptionBundle\Model\OptionInterface $option)
    {
        return $option instanceof \Builderius\MooMoo\Platform\Bundle\OptionBundle\Model\SiteOptionInterface;
    }
    /**
     * @inheritDoc
     */
    public function register(\Builderius\MooMoo\Platform\Bundle\OptionBundle\Model\OptionInterface $option)
    {
        /** @var SiteOptionInterface $option */
        add_site_option($option->getName(), $option->getValue());
    }
}
