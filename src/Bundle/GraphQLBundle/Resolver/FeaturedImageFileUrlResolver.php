<?php

namespace Builderius\Bundle\GraphQLBundle\Resolver;

use Builderius\GraphQL\Type\Definition\ResolveInfo;

class FeaturedImageFileUrlResolver implements GraphQLFieldResolverInterface
{
    /**
     * @inheritDoc
     */
    public function getTypeNames()
    {
        return ['FeaturedImage'];
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
        return 'file_url';
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
        $result = null;
        if (!property_exists($objectValue, 'id') || (property_exists($objectValue, 'size_set') && $objectValue->size_set === true)) {
            return $objectValue->file_url;
        }
        $post = get_post($objectValue->id);
        if ($post instanceof \WP_Post) {
            $meta = get_post_meta($objectValue->id, '', true);
            if (!isset($meta['_wp_attachment_metadata'])) {
                return $result;
            }
            $data = unserialize($meta['_wp_attachment_metadata'][0]);
            $baseUrl = wp_get_upload_dir()['baseurl'];
            $result = sprintf(
                '%s/%s',
                $baseUrl,
                $data['file']
            );
            if (isset($args['size']) && isset($meta['_wp_attachment_metadata'])) {
                if (is_array($data['sizes']) && isset($data['sizes'][$args['size']]) && is_array($data['sizes'][$args['size']])) {
                    $fileFolders = explode('/', $data['file']);
                    $fileFolders[count($fileFolders) - 1] = $data['sizes'][$args['size']]['file'];
                    $result = sprintf(
                        '%s/%s',
                        $baseUrl,
                        implode('/', $fileFolders)
                    );
                }
            }
        }

        return $result;
    }
}