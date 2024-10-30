<?php

namespace Builderius\Bundle\SettingBundle\Model;

use Builderius\Bundle\SettingBundle\Factory\BuilderiusSettingComponentOptionFactory;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag;

class BuilderiusSettingComponent extends ParameterBag implements BuilderiusSettingComponentInterface
{
    const NAME_FIELD = 'name';
    const ACCEPTABLE_OPTIONS_FIELD = 'acceptable_options';

    const ACCEPTABLE_TYPES = ['boolean', 'integer', 'double', 'string', 'array', 'object'];

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->get(self::NAME_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        $this->set(self::NAME_FIELD, $name);
        
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAcceptableOptions()
    {
        return $this->get(self::ACCEPTABLE_OPTIONS_FIELD, []);
    }

    /**
     * @param array $options
     * @return $this
     * @throws \Exception
     */
    public function setAcceptableOptions(array $options)
    {
        $settingOptions = [];
        foreach ($options as $name => $arguments) {
            if (!in_array($arguments['type'], self::ACCEPTABLE_TYPES)) {
                throw new \Exception(
                    sprintf(
                        'There is not correct option type for "%s" setting. Acceptable types are "%s", but "%s" given',
                        $this->getName(),
                        implode('", "', self::ACCEPTABLE_TYPES),
                        $arguments['type']
                    )
                );
            }
            $arguments['name'] = $name;
            $settingOptions[$name] = BuilderiusSettingComponentOptionFactory::create($arguments);
        }
        $this->set(
            self::ACCEPTABLE_OPTIONS_FIELD,
            array_merge($this->getAcceptableOptions(), $settingOptions)
        );

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addAcceptableOption($name, array $arguments)
    {
        $arguments['name'] = $name;
        $settingOptions = $this->getAcceptableOptions();
        $settingOptions[$name] = BuilderiusSettingComponentOptionFactory::create($arguments);
        
        return $this;
    }
}
