<?php

namespace Builderius\Bundle\TemplateBundle\Model;

use Builderius\Bundle\VCSBundle\Model\AbstractBuilderiusVCSOwner;

class BuilderiusTemplate extends AbstractBuilderiusVCSOwner implements BuilderiusTemplateInterface
{
    const SORT_ORDER_FIELD = 'sort_order';
    const APPLY_RULES_CONFIG_FIELD = 'apply_rules_config';
    const BUILDER_MODE_LINK_FIELD = 'builder_mode_link';
    const HOOK_TYPE_FIELD = 'hook_type';
    const HOOK_FIELD = 'hook';
    const HOOK_ACCEPTED_ARGS_FIELD = 'hook_accepted_args';
    const CLEAR_EXISTING_HOOKS_FIELD = 'clear_existing_hooks';
    const SUB_TYPE_FIELD = 'sub_type';

    const SERIALIZED_APPLY_RULES_CONFIG_GRAPHQL = 'serialized_apply_rules_config';

    /**
     * @inheritDoc
     */
    public function getSortOrder()
    {
        return (int)$this->get(self::SORT_ORDER_FIELD, 10);
    }

    /**
     * @inheritDoc
     */
    public function setSortOrder($sortOrder)
    {
        $this->set(self::SORT_ORDER_FIELD, $sortOrder);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getApplyRulesConfig()
    {
        return $this->get(self::APPLY_RULES_CONFIG_FIELD, []);
    }

    /**
     * @inheritDoc
     */
    public function setApplyRulesConfig(array $applyRulesConfig)
    {
        $this->set(self::APPLY_RULES_CONFIG_FIELD, $applyRulesConfig);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBuilderModeLink()
    {
        return $this->get(self::BUILDER_MODE_LINK_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setBuilderModeLink($link)
    {
        $this->set(self::BUILDER_MODE_LINK_FIELD, $link);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSubType()
    {
        return $this->get(self::SUB_TYPE_FIELD, 'default');
    }

    /**
     * @inheritDoc
     */
    public function setSubType($subtype)
    {
        $this->set(self::SUB_TYPE_FIELD, $subtype);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getHookType()
    {
        return $this->get(self::HOOK_TYPE_FIELD, 'action');
    }

    /**
     * @inheritDoc
     */
    public function setHookType($type)
    {
        $this->set(self::HOOK_TYPE_FIELD, $type);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getHook()
    {
        return $this->get(self::HOOK_FIELD, 'builderius_content');
    }

    /**
     * @inheritDoc
     */
    public function setHook($hook)
    {
        $this->set(self::HOOK_FIELD, $hook);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getHookAcceptedArgs()
    {
        return $this->get(self::HOOK_ACCEPTED_ARGS_FIELD, 1);
    }

    /**
     * @inheritDoc
     */
    public function setHookAcceptedArgs($argsQty)
    {
        $this->set(self::HOOK_ACCEPTED_ARGS_FIELD, $argsQty);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isClearExistingHooks()
    {
        return $this->get(self::CLEAR_EXISTING_HOOKS_FIELD, false);
    }

    /**
     * @inheritDoc
     */
    public function setClearExistingHooks($clearExistingHooks)
    {
        $this->set(self::CLEAR_EXISTING_HOOKS_FIELD, $clearExistingHooks);

        return $this;
    }
}
