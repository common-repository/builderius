<?php

namespace Builderius\Bundle\TemplateBundle\DynamicDataHelper;

use Builderius\Bundle\TemplateBundle\ApplyRule\Provider\ApplyRuleArgumentsProviderInterface;

interface DynamicDataHelperArgumentInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     * @return $this
     */
    public function setType($type);

    /**
     * @return mixed
     */
    public function getValue();

    /**
     * @param mixed $value
     * @return $this
     */
    public function setValue($value);

    /**
     * @return array|null
     */
    public function getValueList();

    /**
     * @param array $valueList
     * @return $this
     */
    public function setValueList(array $valueList);

    public function setValueListProvider(ApplyRuleArgumentsProviderInterface $valueListProvider);

    /**
     * @return string
     */
    public function getPlaceholder();

    /**
     * @param string $placeholder
     * @return $this
     */
    public function setPlaceholder($placeholder);

    /**
     * @return bool
     */
    public function isEnum();

    /**
     * @param bool $enum
     * @return $this
     */
    public function setEnum($enum);
}