<?php

namespace Builderius\Bundle\TemplateBundle\Provider\DataVar;

class CompositeDataVarValueGenerator implements DataVarValueGeneratorInterface
{
    /**
     * @var DataVarValueGeneratorInterface[]
     */
    private $generators = [];

    public function addGenerator(DataVarValueGeneratorInterface $generator)
    {
        $this->generators[$generator->getType()] = $generator;
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return true;
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
    public function generateValue($templateType, $dataVarName,  array $dataVarsConfigs, array $dataVarsValues)
    {
        if (isset($dataVarsValues[$dataVarName])) {
            return $dataVarsValues;
        }
        if (isset($dataVarsConfigs[$dataVarName])) {
            $dataVarConfig = $dataVarsConfigs[$dataVarName];
            $dataVarType = $dataVarConfig['type'];
            if (isset($this->generators[$dataVarType])) {
                $generator = $this->generators[$dataVarType];
                $dependsOnDataVars = $generator->getDependsOnDataVars($dataVarConfig);
                if (!empty($dependsOnDataVars)) {
                    foreach ($dependsOnDataVars as $dependsOnDataVarName) {
                        $dataVarsValues = $this->generateValue(
                            $templateType,
                            $dependsOnDataVarName,
                            $dataVarsConfigs,
                            $dataVarsValues
                        );
                    }
                }
                $dataVarsValues = $generator->generateValue(
                    $templateType,
                    $dataVarName,
                    $dataVarsConfigs,
                    $dataVarsValues
                );
            } else {
                $dataVarsValues[$dataVarConfig['name']] = $dataVarConfig['value'];
            }
        }

        return $dataVarsValues;
    }
}