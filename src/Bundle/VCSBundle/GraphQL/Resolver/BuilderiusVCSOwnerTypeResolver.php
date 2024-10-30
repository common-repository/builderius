<?php

namespace Builderius\Bundle\VCSBundle\GraphQL\Resolver;

use Builderius\Bundle\GraphQLBundle\Provider\Type\GraphQLTypesProviderInterface;
use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLTypeResolverInterface;
use Builderius\Bundle\VCSBundle\Event\VCSOwnerTypeResolvingEvent;
use Builderius\Bundle\VCSBundle\Model\BuilderiusVCSOwnerInterface;
use Builderius\GraphQL\Type\Definition\ResolveInfo;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\EventDispatcher\EventDispatcher;

class BuilderiusVCSOwnerTypeResolver implements GraphQLTypeResolverInterface
{
    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @var GraphQLTypesProviderInterface
     */
    private $typesProvider;

    /**
     * @param EventDispatcher $eventDispatcher
     * @param GraphQLTypesProviderInterface $typesProvider
     */
    public function __construct(EventDispatcher $eventDispatcher, GraphQLTypesProviderInterface $typesProvider)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->typesProvider = $typesProvider;
    }

    /**
     * @inheritDoc
     */
    public function resolve($value, $context, ResolveInfo $info)
    {
        if ($value instanceof BuilderiusVCSOwnerInterface) {
            $event = new VCSOwnerTypeResolvingEvent($value);
            $this->eventDispatcher->dispatch($event, 'builderius_vcs_owner_graphql_type_resolving');
            $type = $event->getTypeName();
            if ($type) {
                return $this->typesProvider->getType($type);
            }
        }

        return $this->typesProvider->getType('BuilderiusTemplate');
    }
}