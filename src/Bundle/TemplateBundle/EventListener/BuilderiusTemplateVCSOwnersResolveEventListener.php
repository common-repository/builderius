<?php

namespace Builderius\Bundle\TemplateBundle\EventListener;

use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLFieldResolverInterface;
use Builderius\Bundle\VCSBundle\Event\BuilderiusVCSOwnersResolveEvent;

class BuilderiusTemplateVCSOwnersResolveEventListener
{
    /**
     * @var GraphQLFieldResolverInterface
     */
    private $templateResolver;

    /**
     * @param GraphQLFieldResolverInterface $templateResolver
     */
    public function __construct(
        GraphQLFieldResolverInterface $templateResolver
    ) {
        $this->templateResolver = $templateResolver;
    }

    /**
     * @param BuilderiusVCSOwnersResolveEvent $event
     */
    public function onResolve(BuilderiusVCSOwnersResolveEvent $event)
    {
        $args = $event->getArgs();
        $args['standalone'] = true;
        $templates = $this->templateResolver->resolve(
            $event->getObjectValue(),
            $args,
            $event->getContext(),
            $event->getInfo()
        );
        $owners = array_merge($event->getVscOwners(), $templates);
        $event->setVscOwners($owners);
    }
}