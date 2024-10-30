<?php

namespace Builderius\Bundle\SettingBundle\Checker\Setting\Chain\Element;

use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingInterface;

class BuilderiusSettingCheckerSettingValueSchemaChainElement extends
 AbstractBuilderiusSettingCheckerChainElement
{
    const ALLOWED_KEYS = [
        'type' => 'string',
        'validators' => 'array'
    ];

    /**
     * @inheritDoc
     */
    public function checkSetting(BuilderiusSettingInterface $setting)
    {
        $valueSchema = $setting->getValueSchema();
        if ($valueSchema === null || empty($valueSchema)) {
            throw new \Exception(
                sprintf(
                    'There is no required property value_schema for setting. Problem found in setting "%s"',
                    $setting->getName()
                )
            );
        }
        foreach ($valueSchema as $valueArguments) {
            foreach ($valueArguments as $key => $value) {
                if (!in_array($key, array_keys(self::ALLOWED_KEYS))) {
                    throw new \Exception(
                        sprintf(
                            'Not allowed value_schema argument "%s" found, allowed arguments are "%s".
                            Problem found in setting "%s"',
                            $key,
                            implode(', ', array_keys(self::ALLOWED_KEYS)),
                            $setting->getName()
                        )
                    );
                }
                if (gettype($value) !== self::ALLOWED_KEYS[$key] &&
                    (self::ALLOWED_KEYS[$key] === 'number' && !in_array(gettype($value), ['integer', 'double']))) {
                    throw new \Exception(
                        sprintf(
                            'Type of value_schema argument "%s" should be "%s", but "%s" provided.
                            Problem found in setting "%s"',
                            $key,
                            self::ALLOWED_KEYS[$key],
                            gettype($value),
                            $setting->getName()
                        )
                    );
                }
            }
        }
        
        return true;
    }
}
