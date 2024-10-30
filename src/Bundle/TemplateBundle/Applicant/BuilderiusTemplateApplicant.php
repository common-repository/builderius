<?php

namespace Builderius\Bundle\TemplateBundle\Applicant;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag;

class BuilderiusTemplateApplicant extends ParameterBag implements BuilderiusTemplateApplicantInterface
{
    const LABEL_FIELD = 'label';
    const URL_FIELD = 'url';
    const DATA_FIELD = 'data';
    const PARAMETERS_FIELD = 'params';
    const GROUP_LABEL_FIELD = 'groupLabel';
    const CATEGORY_FIELD = 'category';
    const SORT_ORDER_FIELD = 'sortOrder';

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
    public function getUrl()
    {
        return $this->get(self::URL_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setUrl($url)
    {
        $this->set(self::URL_FIELD, $url);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getData()
    {
        return $this->get(self::DATA_FIELD, []);
    }

    /**
     * @inheritDoc
     */
    public function setData(array $data)
    {
        $this->set(self::DATA_FIELD, $data);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getParameters()
    {
        return $this->get(self::PARAMETERS_FIELD, []);
    }

    /**
     * @inheritDoc
     */
    public function setParameters(array $parameters)
    {
        $this->set(self::PARAMETERS_FIELD, $parameters);

        return $this;
    }

    /**
     * TODO: delete after 0.12.0
     */
    public function getGroupLabel()
    {
        return $this->get(self::GROUP_LABEL_FIELD);
    }

    /**
     * TODO: delete after 0.12.0
     */
    public function setGroupLabel($groupLabel)
    {
        $this->set(self::GROUP_LABEL_FIELD, $groupLabel);

        return $this;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->get(self::CATEGORY_FIELD, 'other');
    }

    /**
     * @param string $category
     * @return $this
     */
    public function setCategory($category)
    {
        $this->set(self::CATEGORY_FIELD, $category);

        return $this;
    }

    /**
     * @return int
     */
    public function getSortOrder()
    {
        return $this->get(self::SORT_ORDER_FIELD, 10);
    }

    /**
     * @param int $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder)
    {
        $this->set(self::CATEGORY_FIELD, $sortOrder);

        return $this;
    }
}