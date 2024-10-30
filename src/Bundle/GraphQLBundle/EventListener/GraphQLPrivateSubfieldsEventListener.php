<?php

namespace Builderius\Bundle\GraphQLBundle\EventListener;

use Builderius\Bundle\GraphQLBundle\Event\GraphQLSubfieldsResolvedEvent;
use Builderius\GraphQL\Language\AST\ArgumentNode;
use Builderius\GraphQL\Language\AST\DirectiveNode;
use Builderius\GraphQL\Language\AST\FieldNode;
use Builderius\GraphQL\Language\AST\NodeList;

class GraphQLPrivateSubfieldsEventListener
{
    /**
     * @param GraphQLSubfieldsResolvedEvent $event
     */
    public function onSubfieldsResolved(GraphQLSubfieldsResolvedEvent $event)
    {
        $fields = $event->getFieldNodes();
        $results = $event->getResults();
        foreach ($fields as $fieldNodes) {
            /** @var FieldNode $fieldNode */
            foreach ($fieldNodes as $fieldNode) {
                $name = null !== $fieldNode->alias ? $fieldNode->alias->value : $fieldNode->name->value;
                $isPrivate = $this->isPrivate($fieldNode->arguments, $fieldNode->directives);
                if ($isPrivate) {
                    unset($results[$name]);
                }
            }
        }
        $event->setResults($results);
    }

    /**
     * @param NodeList $arguments
     * @param NodeList $directives
     * @return bool
     */
    private function isPrivate(NodeList $arguments, NodeList $directives)
    {
        $isPrivate = false;
        /** @var ArgumentNode $argument */
        foreach ($arguments as $argument) {
            if ($argument->name->value === 'private' && $argument->value->value == true) {
                $isPrivate = true;
                break;
            }
        }
        /** @var DirectiveNode $directive */
        foreach ($directives as $directive) {
            if ($directive->name->value === 'private') {
                $isPrivate = true;
                break;
            }
        }

        return $isPrivate;
    }
}