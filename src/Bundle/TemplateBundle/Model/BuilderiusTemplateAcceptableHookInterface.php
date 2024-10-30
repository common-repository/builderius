<?php

namespace Builderius\Bundle\TemplateBundle\Model;

interface BuilderiusTemplateAcceptableHookInterface
{
    /**
     * @return string
     */
    public function getType();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return int
     */
    public function getAcceptedArgs();

    /**
     * @return string
     */
    public function isPossibleToClearHooks();
}