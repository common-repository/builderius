<?php

namespace Builderius\Bundle\TemplateBundle\Model;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag;

class BuilderiusTemplateAcceptableHook extends ParameterBag implements BuilderiusTemplateAcceptableHookInterface
{
    const TYPE_FIELD = 'type';
    const NAME_FIELD = 'name';
    const ACCEPTED_ARGS_FIELD = 'accepted_args';
    const POSSIBLE_TO_CLEAR_HOOKS = 'possible_to_clean_hooks';

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return $this->get(self::TYPE_FIELD, 'action');
    }

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
    public function getAcceptedArgs()
    {
        return $this->get(self::ACCEPTED_ARGS_FIELD, 1);
    }

    /**
     * @inheritDoc
     */
    public function isPossibleToClearHooks()
    {
        return $this->get(self::POSSIBLE_TO_CLEAR_HOOKS, true);
    }
}
