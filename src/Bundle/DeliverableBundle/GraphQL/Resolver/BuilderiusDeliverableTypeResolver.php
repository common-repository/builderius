<?php

namespace Builderius\Bundle\DeliverableBundle\GraphQL\Resolver;

use Builderius\Bundle\DeliverableBundle\Event\DeliverableTypeResolvingEvent;
use Builderius\Bundle\DeliverableBundle\Model\BuilderiusDeliverableInterface;
use Builderius\Bundle\GraphQLBundle\Provider\Type\GraphQLTypesProviderInterface;
use Builderius\GraphQL\Type\Definition\ResolveInfo;
use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLTypeResolverInterface;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\EventDispatcher\EventDispatcher;

class BuilderiusDeliverableTypeResolver implements GraphQLTypeResolverInterface
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
        if ($value instanceof BuilderiusDeliverableInterface) {
            $event = new DeliverableTypeResolvingEvent($value);
            $this->eventDispatcher->dispatch($event, 'builderius_deliverable_graphql_type_resolving');
            $type = $event->getTypeName();
            if ($type) {
                return $this->typesProvider->getType($type);
            }
        }

        return $this->typesProvider->getType('BuilderiusRelease');
    }
}