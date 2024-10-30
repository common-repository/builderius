<?php

namespace Builderius\Bundle\TemplateBundle\Model;

use Builderius\Bundle\VCSBundle\Model\BuilderiusVCSOwnerInterface;

interface BuilderiusTemplateInterface extends BuilderiusVCSOwnerInterface
{
    /**
     * @return string
     */
    public function getBuilderModeLink();

    /**
     * @param string $link
     * @return $this
     */
    public function setBuilderModeLink($link);

    /**
     * @return string
     */
    public function getSubType();

    /**
     * @param string $subtype
     * @return $this
     */
    public function setSubType($subtype);

    /**
     * @return string|null
     */
    public function getHookType();

    /**
     * @param string $type
     * @return $this
     */
    public function setHookType($type);

    /**
     * @return string|null
     */
    public function getHook();

    /**
     * @param string $hook
     * @return $this
     */
    public function setHook($hook);

    /**
     * @return string|null
     */
    public function getHookAcceptedArgs();

    /**
     * @param int $argsQty
     * @return $this
     */
    public function setHookAcceptedArgs($argsQty);

    /**
     * @return bool
     */
    public function isClearExistingHooks();

    /**
     * @param bool $clearExistingHooks
     * @return $this
     */
    public function setClearExistingHooks($clearExistingHooks);

    /**
     * @return int
     */
    public function getSortOrder();

    /**
     * @param int $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder);

    /**
     * @return array
     */
    public function getApplyRulesConfig();

    /**
     * @param array $applyRulesConfig
     * @return $this
     */
    public function setApplyRulesConfig(array $applyRulesConfig);
}
