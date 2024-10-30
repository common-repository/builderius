<?php

namespace Builderius\Bundle\TemplateBundle\EventListener;

use Builderius\Bundle\ModuleBundle\Event\DynamicDataConditionEvaluationEvent;
use Builderius\Bundle\TemplateBundle\Twig\TemplateDataVarsExtension;

class DynamicDataConditionEvaluationEventListener
{
    /**
     * @var TemplateDataVarsExtension
     */
    private $templateDataVarsTwigExtension;

    /**
     * @param TemplateDataVarsExtension $templateDataVarsTwigExtension
     */
    public function __construct(
        TemplateDataVarsExtension $templateDataVarsTwigExtension
    ) {
        $this->templateDataVarsTwigExtension = $templateDataVarsTwigExtension;
    }

    /**
     * @param DynamicDataConditionEvaluationEvent $event
     * @return void
     */
    public function onDynamicDataConditionEvaluation(DynamicDataConditionEvaluationEvent $event)
    {
        $function = $event->getFunction();
        $arguments = $event->getArguments();
        if ($function === 'builderius_data_var') {
            $event->setValue($this->templateDataVarsTwigExtension->getNonEscapedDataVarValue($arguments[0], false));
        } elseif ($function === 'builderius_data_var_escaped') {
            $event->setValue($this->templateDataVarsTwigExtension->getEscapedDataVarValue($arguments[0]));
        }
    }
}