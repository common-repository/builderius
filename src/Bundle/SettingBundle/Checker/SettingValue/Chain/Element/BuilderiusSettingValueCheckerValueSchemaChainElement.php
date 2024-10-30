<?php

namespace Builderius\Bundle\SettingBundle\Checker\SettingValue\Chain\Element;

use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingValueInterface;
use Builderius\Respect\Validation\Factory;
use Builderius\Respect\Validation\Validatable;
use Builderius\Respect\Validation\Validator as v;

class BuilderiusSettingValueCheckerValueSchemaChainElement extends AbstractBuilderiusSettingValueCheckerChainElement
{
    /**
     * @inheritDoc
     */
    protected function checkValue(
        BuilderiusSettingValueInterface $settingValue,
        BuilderiusSettingInterface $setting
    )
    {
        $value = $settingValue->getValue();
        if (!is_array($value)) {
            throw new \Exception(
                sprintf(
                    'Setting value is not applicable to schema.
                         Problem found in setting "%s"',
                    $setting->getName()
                )
            );
        }
        $this->checkValueItem($settingValue, $setting, $value);
    }

    /**
     * @param BuilderiusSettingValueInterface $settingValue
     * @param BuilderiusSettingInterface $setting
     * @param array $value
     * @return bool
     * @throws \Exception
     */
    protected function checkValueItem(
        BuilderiusSettingValueInterface $settingValue,
        BuilderiusSettingInterface $setting,
        array $value
    ) {
        $valueSchema = $setting->getValueSchema();

        Factory::setDefaultInstance(
            (new Factory())
                ->withRuleNamespace('Builderius\\Bundle\\SettingBundle\\Validation\\Rule')
                ->withExceptionNamespace('Builderius\\Bundle\\SettingBundle\\Validation\\Exception')
        );
        if (isset($valueSchema['validators']) && !empty($valueSchema['validators'])) {
            $objValidator = v::allOf(v::arrayType());
            foreach ($valueSchema['validators'] as $validator) {
                if (isset($validator['params']) && !empty($validator['params'])) {
                    /** @var Validatable $attrValidator */
                    $attrValidator = forward_static_call_array(
                        [v::class, $validator['type']],
                        $validator['params']
                    );
                } else {
                    $attrValidator = forward_static_call(
                        [v::class, $validator['type']]
                    );
                }
                if (isset($validator['message']) && !empty($validator['message'])) {
                    $attrValidator->setTemplate($validator['message']);
                }
                $objValidator->addRule($attrValidator);
            }
            $objValidator->assert($value);
        }
        foreach ($value as $key => $val) {
            if ($val !== null) {
                $attr = $valueSchema[$key];
                $allAttributesValidator = v::allOf(v::{$attr['type'] . 'Type'}());
                if (isset($attr['validators']) && !empty($attr['validators'])) {
                    foreach ($attr['validators'] as $validator) {
                        if (isset($validator['params']) && !empty($validator['params'])) {
                            /** @var Validatable $attrValidator */
                            $attrValidator = forward_static_call_array(
                                [v::class, $validator['type']],
                                $validator['params']
                            );
                        } else {
                            $attrValidator = forward_static_call(
                                [v::class, $validator['type']]
                            );
                        }
                        if (isset($validator['message']) && !empty($validator['message'])) {
                            $attrValidator->setTemplate($validator['message']);
                        }
                        $allAttributesValidator->addRule($attrValidator);
                    }
                }
                $allAttributesValidator->assert($val);
                if ($attr['type'] === 'array' && isset($attr['of']) && isset($attr['of']['type']) &&
                    isset($attr['of']) && isset($attr['of']['shape'])) {
                    if (isset($attr['validators']) && !empty($attr['validators'])) {
                        $validators = $attr['validators'];
                        $arrayValidator = v::allOf(v::arrayType());
                        foreach ($validators as $validator) {
                            if (isset($validator['params']) && !empty($validator['params'])) {
                                /** @var Validatable $attrValidator */
                                $attrValidator = forward_static_call_array(
                                    [v::class, $validator['type']],
                                    $validator['params']
                                );
                            } else {
                                $attrValidator = forward_static_call(
                                    [v::class, $validator['type']]
                                );
                            }
                            if (isset($validator['message']) && !empty($validator['message'])) {
                                $attrValidator->setTemplate($validator['message']);
                            }
                            $arrayValidator->addRule($attrValidator);
                        }
                        $arrayValidator->assert($val);
                    }
                    $shape = $attr['of']['shape'];
                    foreach ($val as $itemVal) {
                        if (isset($attr['of']['validators']) && !empty($attr['of']['validators'])) {
                            $validators = $attr['of']['validators'];
                            $objValidator = v::allOf(v::arrayType());
                            foreach ($validators as $validator) {
                                if (isset($validator['params']) && !empty($validator['params'])) {
                                    /** @var Validatable $attrValidator */
                                    $attrValidator = forward_static_call_array(
                                        [v::class, $validator['type']],
                                        $validator['params']
                                    );
                                } else {
                                    $attrValidator = forward_static_call(
                                        [v::class, $validator['type']]
                                    );
                                }
                                if (isset($validator['message']) && !empty($validator['message'])) {
                                    $attrValidator->setTemplate($validator['message']);
                                }
                                $objValidator->addRule($attrValidator);
                            }
                            $objValidator->assert($itemVal);
                        }
                        foreach ($itemVal as $key => $propVal) {
                            if ($propVal !== null) {
                                $propAttr = $shape[$key];
                                $allPropAttributesValidator = v::allOf(v::{$propAttr['type'] . 'Type'}());
                                if (isset($propAttr['validators']) && !empty($propAttr['validators'])) {
                                    foreach ($propAttr['validators'] as $validator) {
                                        if (isset($validator['params']) && !empty($validator['params'])) {
                                            /** @var Validatable $propAttrValidator */
                                            $propAttrValidator = forward_static_call_array(
                                                [v::class, $validator['type']],
                                                $validator['params']
                                            );
                                        } else {
                                            $propAttrValidator = forward_static_call(
                                                [v::class, $validator['type']]
                                            );
                                        }
                                        if (isset($validator['message']) && !empty($validator['message'])) {
                                            $propAttrValidator->setTemplate($validator['message']);
                                        }
                                        $allPropAttributesValidator->addRule($propAttrValidator);
                                    }
                                }
                                $allPropAttributesValidator->assert($propVal);
                            }
                        }
                    }
                }
            }
        }

        return true;
    }
}
