<?php

namespace Builderius\Bundle\GraphQLBundle\Config;

interface GraphQLFieldArgumentConfigInterface
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
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description);

    /**
     * @return mixed
     */
    public function getDefaultValue();

    /**
     * @param mixed $defaultValue
     * @return $this
     */
    public function setDefaultValue($defaultValue);
}