<?php

namespace Builderius\Bundle\GraphQLBundle\Resolver;

use Builderius\GraphQL\Type\Definition\ResolveInfo;

class PostFeaturedImageResolver implements GraphQLFieldResolverInterface
{
    /**
     * @inheritDoc
     */
    public function getTypeNames()
    {
        return ['Post'];
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
        return 'featured_image';
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
        $result = new \stdClass();
        $result->title = null;
        $result->alt_text = null;
        $result->caption = null;
        $result->description = null;
        $result->file_url = null;
        if (isset($args['size'])) {
            $result->size_set = true;
        }
        if (!has_post_thumbnail($objectValue)) {
            return $result;
        }
        /** @var \WP_Post $objectValue */
        $id = get_post_thumbnail_id($objectValue);
        $result->id = $id;
        $post = get_post($id);
        if ($post instanceof \WP_Post) {
            $result->title = $post->post_title;
            $result->caption = $post->post_excerpt;
            $result->description = $post->post_content;
            $meta = get_post_meta($id, '', true);
            if (!isset($meta['_wp_attachment_metadata'])) {
                return $result;
            }
            if (isset($meta['_wp_attachment_image_alt'])) {
                $result->alt_text = $meta['_wp_attachment_image_alt'][0];
            }
            $data = unserialize($meta['_wp_attachment_metadata'][0]);
            $baseUrl = wp_get_upload_dir()['baseurl'];
            $result->file_url = sprintf(
                '%s/%s',
                $baseUrl,
                $data['file']
            );
            if (isset($args['size']) && isset($meta['_wp_attachment_metadata'])) {
                if (is_array($data['sizes']) && isset($data['sizes'][$args['size']]) && is_array($data['sizes'][$args['size']])) {
                    $fileFolders = explode('/', $data['file']);
                    $fileFolders[count($fileFolders) - 1] = $data['sizes'][$args['size']]['file'];
                    $result->file_url = sprintf(
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