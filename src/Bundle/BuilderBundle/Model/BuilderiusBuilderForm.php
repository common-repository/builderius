<?php

namespace Builderius\Bundle\BuilderBundle\Model;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag;

class BuilderiusBuilderForm extends ParameterBag implements BuilderiusBuilderFormInterface
{
    const NAME_FIELD = 'name';
    const LABEL_FIELD = 'label';
    const SORT_ORDER_FIELD = 'sortOrder';

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->get(self::NAME_FIELD);
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return __($this->get(self::LABEL_FIELD), 'builderius');
    }

    /**
     * @inheritDoc
     */
    public function getSortOrder()
    {
        return $this->get(self::SORT_ORDER_FIELD, 0);
    }
}
