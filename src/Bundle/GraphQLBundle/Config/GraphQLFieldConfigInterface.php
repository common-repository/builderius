<?php

namespace Builderius\Bundle\GraphQLBundle\Config;

use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLFieldResolverInterface;

interface GraphQLFieldConfigInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name);
    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     * @return $this
     */
    public function setType($type);

    /**
     * @return GraphQLFieldArgumentConfigInterface[]
     */
    public function getArguments();

    /**
     * @param GraphQLFieldArgumentConfigInterface[] $arguments
     * @return $this
     */
    public function setArguments(array $arguments);

    /**
     * @param GraphQLFieldArgumentConfigInterface $argument
     * @return $this
     */
    public function addArgument(GraphQLFieldArgumentConfigInterface $argument);

    /**
     * @return GraphQLFieldResolverInterface
     */
    public function getResolver();

    /**
     * @param GraphQLFieldResolverInterface $resolver
     * @return $this
     */
    public function setResolver(GraphQLFieldResolverInterface $resolver);
}