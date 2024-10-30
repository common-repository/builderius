<?php

namespace Builderius\Bundle\GraphQLBundle\Resolver;

use Builderius\Bundle\ExpressionLanguageBundle\ExpressionLanguage;
use Builderius\GraphQL\Cache\GraphQLObjectCache;
use Builderius\GraphQL\Type\Definition\ResolveInfo;

class OptionValueResolver extends AbstractLocalVarsAwareResolver
{
    /**
     * @inheritDoc
     */
    public function getTypeNames()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getSortOrder()
    {
        return 10;
    }

    /**
     * @inheritDoc
     */
    public function getFieldName()
    {
        return 'option_value';
    }

    /**
     * @inheritDoc
     */
    public function isApplicable($objectValue, array $args, $context, ResolveInfo $info)
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function resolve($objectValue, array $args, $context, ResolveInfo $info)
    {
        if (!isset($args['name'])) {
            return null;
        }
        $args = $this->processArguments($args, $info->path);

        return get_option($args['name'], null);
    }
}