<?php

namespace Builderius\Bundle\TemplateBundle\GraphQL\Resolver;

use Builderius\Bundle\TemplateBundle\Event\PostContainingEvent;
use Builderius\Bundle\TemplateBundle\Model\BuilderiusTemplate;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplatePostType;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplateSubTypeTaxonomy;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplateTechnologyTaxonomy;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplateTypeTaxonomy;
use Builderius\GraphQL\Type\Definition\ResolveInfo;

class BuilderiusRootMutationFieldCreateTemplateResolver extends AbstractBuilderiusRootMutationFieldCUTemplateResolver
{
    /**
     * @inheritDoc
     */
    public function getFieldName()
    {
        return 'createTemplate';
    }

    /**
     * @inheritDoc
     */
    public function resolve($objectValue, array $args, $context, ResolveInfo $info)
    {
        $input = $args['input'];
        if (isset($input[BuilderiusTemplate::NAME_FIELD])) {
            $postsWithSameName = $this->wpQuery->query([
                'post_type' => BuilderiusTemplatePostType::POST_TYPE,
                'name' => $input[BuilderiusTemplate::NAME_FIELD],
                'post_status' => get_post_stati(),
                'posts_per_page' => 1,
                'no_found_rows' => true,
            ]);
            if (!empty($postsWithSameName)) {
                throw new \Exception('Template with same name already exists.', 400);
            }
        }
        if (isset($input[BuilderiusTemplate::TITLE_FIELD])) {
            $postsWithSameTitle = $this->wpQuery->query([
                'post_type' => BuilderiusTemplatePostType::POST_TYPE,
                'title' => $input[BuilderiusTemplate::TITLE_FIELD],
                'post_status' => get_post_stati(),
                'posts_per_page' => 1,
                'no_found_rows' => true,
            ]);
            if (!empty($postsWithSameTitle)) {
                throw new \Exception('Template with same title already exists.', 400);
            }
        }
        $preparedPost = $this->getPreparedPost($input);
        $preparedPost->post_date = current_time( 'mysql' );
        $preparedPost->post_date_gmt = $preparedPost->post_date;

        $postId = wp_insert_post(wp_slash((array)$preparedPost), true);

        if (is_wp_error($postId)) {
            /** @var \WP_Error $postId */
            if ('db_insert_error' === $postId->get_error_code()) {
                throw new \Exception($postId->get_error_message(), 500);
            } else {
                throw new \Exception($postId->get_error_message(), 400);
            }
        }
        if ($preparedPost->{BuilderiusTemplate::SORT_ORDER_FIELD}) {
            update_post_meta(
                $postId,
                BuilderiusTemplate::SORT_ORDER_FIELD,
                $preparedPost->{BuilderiusTemplate::SORT_ORDER_FIELD}
            );
        }
        if (property_exists($preparedPost, BuilderiusTemplate::APPLY_RULES_CONFIG_FIELD) &&
            $preparedPost->{BuilderiusTemplate::APPLY_RULES_CONFIG_FIELD}) {
            update_post_meta(
                $postId,
                BuilderiusTemplate::APPLY_RULES_CONFIG_FIELD,
                $preparedPost->{BuilderiusTemplate::APPLY_RULES_CONFIG_FIELD}
            );
        }
        if (property_exists($preparedPost, BuilderiusTemplate::ACTIVE_BRANCH_NAME_FIELD) &&
            $preparedPost->{BuilderiusTemplate::ACTIVE_BRANCH_NAME_FIELD}) {
            update_post_meta(
                $postId,
                BuilderiusTemplate::ACTIVE_BRANCH_NAME_FIELD,
                $preparedPost->{BuilderiusTemplate::ACTIVE_BRANCH_NAME_FIELD}
            );
        }
        if ($preparedPost->{BuilderiusTemplate::SUB_TYPE_FIELD} === 'hook') {
            update_post_meta(
                $postId,
                BuilderiusTemplate::HOOK_FIELD,
                $preparedPost->{BuilderiusTemplate::HOOK_FIELD}
            );
            update_post_meta(
                $postId,
                BuilderiusTemplate::HOOK_TYPE_FIELD,
                $preparedPost->{BuilderiusTemplate::HOOK_TYPE_FIELD}
            );
            update_post_meta(
                $postId,
                BuilderiusTemplate::HOOK_ACCEPTED_ARGS_FIELD,
                $preparedPost->{BuilderiusTemplate::HOOK_ACCEPTED_ARGS_FIELD}
            );
            update_post_meta(
                $postId,
                BuilderiusTemplate::CLEAR_EXISTING_HOOKS_FIELD,
                $preparedPost->{BuilderiusTemplate::CLEAR_EXISTING_HOOKS_FIELD}
            );
        }

        $post = get_post($postId);

        $subTypeTermsUpdate = $this->handleTerms(
            $post->ID,
            $preparedPost->{BuilderiusTemplate::SUB_TYPE_FIELD},
            BuilderiusTemplateSubTypeTaxonomy::NAME
        );

        if (is_wp_error($subTypeTermsUpdate)) {
            throw new \Exception($subTypeTermsUpdate->get_error_message(), 400);
        }

        $typeTermsUpdate = $this->handleTerms(
            $post->ID,
            $preparedPost->{BuilderiusTemplate::TYPE_FIELD},
            BuilderiusTemplateTypeTaxonomy::NAME
        );

        if (is_wp_error($typeTermsUpdate)) {
            throw new \Exception($typeTermsUpdate->get_error_message(), 400);
        }
        $technologyTermsUpdate = $this->handleTerms(
            $post->ID,
            $preparedPost->{BuilderiusTemplate::TECHNOLOGY_FIELD},
            BuilderiusTemplateTechnologyTaxonomy::NAME
        );

        if (is_wp_error($technologyTermsUpdate)) {
            throw new \Exception($technologyTermsUpdate->get_error_message(), 400);
        }

        $this->eventDispatcher->dispatch(new PostContainingEvent($post), 'builderius_template_created');

        $template = $this->templateFactory->createTemplate($post);

        return new \ArrayObject(['template' => $template]);
    }
}