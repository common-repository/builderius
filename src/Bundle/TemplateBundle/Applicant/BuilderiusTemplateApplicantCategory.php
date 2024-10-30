<?php

namespace Builderius\Bundle\TemplateBundle\Applicant;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag;

class BuilderiusTemplateApplicantCategory extends ParameterBag implements BuilderiusTemplateApplicantCategoryInterface
{
    const NAME_FIELD = 'name';
    const LABEL_FIELD = 'label';
    const SORT_ORDER_FIELD = 'sortOrder';

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->get(self::NAME_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        $this->set(self::NAME_FIELD, $name);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getLabel()
    {
        return $this->get(self::LABEL_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setLabel($label)
    {
        $this->set(self::LABEL_FIELD, $label);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSortOrder()
    {
        return $this->get(self::SORT_ORDER_FIELD, 10);
    }

    /**
     * @inheritDoc
     */
    public function setSortOrder($sortOrder)
    {
        $this->set(self::SORT_ORDER_FIELD, $sortOrder);

        return $this;
    }
}