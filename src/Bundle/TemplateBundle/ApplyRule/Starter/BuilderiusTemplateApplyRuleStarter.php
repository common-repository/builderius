<?php

namespace Builderius\Bundle\TemplateBundle\ApplyRule\Starter;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag;

class BuilderiusTemplateApplyRuleStarter extends ParameterBag implements BuilderiusTemplateApplyRuleStarterInterface
{
    const NAME_FIELD = 'name';
    const TITLE_FIELD = 'title';
    const CATEGORY_FIELD = 'category';
    const TEMPLATE_TYPES_FIELD = 'template_types';
    const TECHNOLOGIES_FIELD = 'technologies';
    const CONFIG_FIELD = 'config';
    const IS_VALID_FIELD = 'is_valid';

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
    public function getTitle()
    {
        return $this->get(self::TITLE_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function getCategory()
    {
        return $this->get(self::CATEGORY_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function getTemplateTypes()
    {
        return $this->get(self::TEMPLATE_TYPES_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function getTechnologies()
    {
        return $this->get(self::TECHNOLOGIES_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function getConfig()
    {
        return $this->get(self::CONFIG_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function isValid()
    {
        return $this->get(self::IS_VALID_FIELD, true);
    }
}