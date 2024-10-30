<?php

namespace Builderius\Bundle\TemplateBundle\DynamicDataHelper;

use Builderius\Bundle\TemplateBundle\ApplyRule\Provider\ApplyRuleArgumentsProviderInterface;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag;

class DynamicDataHelperArgument extends ParameterBag implements DynamicDataHelperArgumentInterface
{
    const NAME_FIELD = 'name';
    const TYPE_FIELD = 'type';
    const VALUE_LIST_FIELD = 'valueList';
    const VALUE_LIST_PROVIDER_FIELD = 'valueListProvider';
    const VALUE_FIELD = 'value';
    const PLACEHOLDER_FIELD = 'placeholder';
    const ENUM_FIELD = 'enum';

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
    public function getType()
    {
        return $this->get(self::TYPE_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setType($type)
    {
        $this->set(self::TYPE_FIELD, $type);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        return $this->get(self::VALUE_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setValue($value)
    {
        $this->set(self::VALUE_FIELD, $value);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getValueList()
    {
        $valueList = $this->get(self::VALUE_LIST_FIELD, (object)[]);
        if (empty((array)$valueList)) {
            $provider = $this->get(self::VALUE_LIST_PROVIDER_FIELD);
            if($provider) {
                $valueList = $provider->getArguments();
            }
        }

        return $valueList;
    }

    /**
     * @inheritDoc
     */
    public function setValueList(array $valueList)
    {
        $this->set(self::VALUE_LIST_FIELD, $valueList);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setValueListProvider(ApplyRuleArgumentsProviderInterface $valueListProvider)
    {
        $this->set(self::VALUE_LIST_PROVIDER_FIELD, $valueListProvider);

        return $this;
    }
    /**
     * @inheritDoc
     */
    public function getPlaceholder()
    {
        return $this->get(self::PLACEHOLDER_FIELD, '');
    }

    /**
     * @inheritDoc
     */
    public function setPlaceholder($placeholder)
    {
        $this->set(self::PLACEHOLDER_FIELD, $placeholder);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isEnum()
    {
        return (bool)$this->get(self::ENUM_FIELD, false);
    }

    /**
     * @inheritDoc
     */
    public function setEnum($enum)
    {
        $this->set(self::ENUM_FIELD, $enum);

        return $this;
    }
}