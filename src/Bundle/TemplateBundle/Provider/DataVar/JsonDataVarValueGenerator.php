<?php

namespace Builderius\Bundle\TemplateBundle\Provider\DataVar;

class JsonDataVarValueGenerator implements DataVarValueGeneratorInterface
{
    /**
     * @inheritDoc
     */
    public function getType()
    {
        return 'json';
    }

    /**
     * @inheritDoc
     */
    public function getDependsOnDataVars(array $dataVarConfig)
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function generateValue($templateType, $dataVarName, array $dataVarsConfigs, array $dataVarsValues)
    {
        $dataVarConfig = $dataVarsConfigs[$dataVarName];
        $value = $dataVarConfig['value'];
        if (is_string($dataVarConfig['value'])) {
            $value = json_decode($value);
        }
        $dataVarsValues[$dataVarName] = $value;

        return $dataVarsValues;
    }
}