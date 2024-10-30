<?php

namespace Builderius\Bundle\ExpressionLanguageBundle\Provider;

use Builderius\Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Builderius\Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

class WpFunctionsProvider implements ExpressionFunctionProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getFunctions()
    {
        return [
            new ExpressionFunction(
                'has_term',
                function ($context, $term = '', $taxonomy = '', $post = null) {
                    return sprintf('has_term(%s, %s, %s)', $term, $taxonomy, $post);
                },
                function ($context, $term = '', $taxonomy = '', $post = null) {
                    return has_term($term, $taxonomy, $post);
                }
            ),
            new ExpressionFunction(
                'metadata_exists',
                function ($context, $meta_type, $object_id, $meta_key) {
                    return sprintf('metadata_exists(%s, %s, %s)', $meta_type, $object_id, $meta_key);
                },
                function ($context, $meta_type, $object_id, $meta_key) {
                    return metadata_exists($meta_type, $object_id, $meta_key);
                }
            ),
        ];
    }
}