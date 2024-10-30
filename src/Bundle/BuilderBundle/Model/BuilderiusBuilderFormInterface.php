<?php

namespace Builderius\Bundle\BuilderBundle\Model;

interface BuilderiusBuilderFormInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @return int
     */
    public function getSortOrder();
}
