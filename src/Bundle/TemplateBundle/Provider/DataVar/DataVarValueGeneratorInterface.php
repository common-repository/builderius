<?php

namespace Builderius\Bundle\TemplateBundle\Provider\DataVar;

interface DataVarValueGeneratorInterface
{
    /**
     * @return string
     */
    public function getType();

    /**
     * @param array $dataVarConfig
     * @return array
     */
    public function getDependsOnDataVars(array $dataVarConfig);

    /**
     * @param string $templateType
     * @param string $dataVarName
     * @param array $dataVarsConfigs
     * @param array $dataVarsValues
     * @return array
     */
    public function generateValue($templateType, $dataVarName,  array $dataVarsConfigs, array $dataVarsValues);
}