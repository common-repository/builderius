<?php

namespace Builderius\Bundle\SettingBundle\Model;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag;

class BuilderiusSettingPath extends ParameterBag implements BuilderiusSettingPathInterface
{
    const NAME_FIELD = 'name';
    const FORM_FIELD = 'form';
    const TAB_FIELD = 'tab';
    const CATEGORY_FIELD = 'category';

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->get(
            self::NAME_FIELD,
            sprintf(
                '%s.%s.%s',
                $this->getForm()->getName(),
                $this->getTab()->getName(),
                $this->getCategory()->getName()
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getForm()
    {
        return $this->get(self::FORM_FIELD);
    }

    /**
     * {@inheritdoc}
     */
    public function getTab()
    {
        return $this->get(self::TAB_FIELD);
    }

    /**
     * {@inheritdoc}
     */
    public function getCategory()
    {
        return $this->get(self::CATEGORY_FIELD);
    }
}
