<?php

namespace Builderius\Bundle\TemplateBundle\Provider\DataVar;

interface DataVarsFinalValuesProviderInterface
{
    /**
     * @return array
     */
    public function getDataVarsFinalValues();

    /**
     * @param string $dataVarName
     * @return mixed
     */
    public function getDataVarFinalValue($dataVarName);
}