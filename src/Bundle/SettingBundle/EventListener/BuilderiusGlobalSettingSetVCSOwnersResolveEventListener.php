<?php

namespace Builderius\Bundle\SettingBundle\EventListener;

use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLFieldResolverInterface;
use Builderius\Bundle\VCSBundle\Event\BuilderiusVCSOwnersResolveEvent;

class BuilderiusGlobalSettingSetVCSOwnersResolveEventListener
{
    /**
     * @var GraphQLFieldResolverInterface
     */
    private $gssResolver;

    /**
     * @param GraphQLFieldResolverInterface $gssResolver
     */
    public function __construct(
    GraphQLFieldResolverInterface $gssResolver
) {
    $this->gssResolver = $gssResolver;
}

    /**
     * @param BuilderiusVCSOwnersResolveEvent $event
     */
    public function onResolve(BuilderiusVCSOwnersResolveEvent $event)
{
    $gss = $this->gssResolver->resolve(
        $event->getObjectValue(),
        $event->getArgs(),
        $event->getContext(),
        $event->getInfo()
    );
    $owners = array_merge($event->getVscOwners(), $gss);
    $event->setVscOwners($owners);
}
}