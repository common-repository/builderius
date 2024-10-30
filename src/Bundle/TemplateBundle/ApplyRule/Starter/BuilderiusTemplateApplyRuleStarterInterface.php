<?php

namespace Builderius\Bundle\TemplateBundle\ApplyRule\Starter;

interface BuilderiusTemplateApplyRuleStarterInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @return string
     */
    public function getCategory();

    /**
     * @return array
     */
    public function getTemplateTypes();

    /**
     * @return array
     */
    public function getTechnologies();

    /**
     * @return array
     */
    public function getConfig();

    /**
     * @return boolean
     */
    public function isValid();
}
