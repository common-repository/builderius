<?php

namespace Builderius\Bundle\GraphQLBundle\Config;

use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareInterface;
use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareTrait;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag;
use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLFieldResolverInterface;

class GraphQLFieldConfig extends ParameterBag implements GraphQLFieldConfigInterface, ConditionAwareInterface
{
    const NAME_FIELD = 'name';
    const DESCRIPTION_FIELD = 'description';
    const TYPE_FIELD = 'type';
    const ARGUMENTS_FIELD = 'args';
    const RESOLVER_FIELD = 'resolve';

    use ConditionAwareTrait;

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->get(self::DESCRIPTION_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setDescription($description)
    {
        $this->set(self::DESCRIPTION_FIELD, $description);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return $this->get(self::TYPE_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setType($type)
    {
        $this->set(self::TYPE_FIELD, $type);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getArguments()
    {
        $defaultArguments = [
            new GraphQLFieldArgumentConfig([
                GraphQLFieldArgumentConfig::NAME_FIELD => 'private',
                GraphQLFieldArgumentConfig::TYPE_FIELD => 'BooleanOrDataVar',
                GraphQLFieldArgumentConfig::DEFAULT_VALUE_FIELD => false,
                GraphQLFieldArgumentConfig::DESCRIPTION_FIELD => 'Optional argument with default value = false. If value will be true - this field will not be in query resulted json, but it\'s value can be used in other fields as local variable argument.'
            ])
        ];

        return $this->get(self::ARGUMENTS_FIELD, $defaultArguments);
    }

    /**
     * @inheritDoc
     */
    public function setArguments(array $arguments)
    {
        foreach ($arguments as $argument) {
            $this->addArgument($argument);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addArgument(GraphQLFieldArgumentConfigInterface $argument)
    {
        $arguments = $this->getArguments();
        $arguments[$argument->getName()] = $argument;
        $this->set(self::ARGUMENTS_FIELD, $arguments);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getResolver()
    {
        return $this->get(self::RESOLVER_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setResolver(GraphQLFieldResolverInterface $resolver)
    {
        $this->set(self::RESOLVER_FIELD, $resolver);

        return $this;
    }
}