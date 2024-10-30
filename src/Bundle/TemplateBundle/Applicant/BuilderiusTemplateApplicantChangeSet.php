<?php

namespace Builderius\Bundle\TemplateBundle\Applicant;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag;

class BuilderiusTemplateApplicantChangeSet extends ParameterBag implements BuilderiusTemplateApplicantChangeSetInterface
{
    const OBJECT_BEFORE_FIELD = 'objectBefore';
    const OBJECT_AFTER_FIELD = 'objectAfter';
    const ACTION_FIELD = 'action';

    /**
     * @inheritDoc
     */
    public function setObjectBefore($object)
    {
        $this->set(self::OBJECT_BEFORE_FIELD, $object);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getObjectBefore()
    {
        return $this->get(self::OBJECT_BEFORE_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setObjectAfter($object)
    {
        $this->set(self::OBJECT_AFTER_FIELD, $object);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getObjectAfter()
    {
        return $this->get(self::OBJECT_AFTER_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setAction($action)
    {
        $this->set(self::ACTION_FIELD, $action);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAction()
    {
        return $this->get(self::ACTION_FIELD);
    }
}