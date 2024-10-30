<?php

namespace Builderius\Bundle\TemplateBundle\Applicant;

interface BuilderiusTemplateApplicantChangeSetInterface
{
    const CREATE_ACTION = 'create';
    const UPDATE_ACTION = 'update';
    const DELETE_ACTION = 'delete';

    /**
     * @param object $object
     * @return $this
     */
    public function setObjectBefore($object);

    /**
     * @return object
     */
    public function getObjectBefore();

    /**
     * @param object $object
     * @return $this
     */
    public function setObjectAfter($object);

    /**
     * @return object
     */
    public function getObjectAfter();

    /**
     * @param string $action
     * @return $this
     */
    public function setAction($action);

    /**
     * @return string
     */
    public function getAction();
}