<?php

namespace Builderius\Bundle\TemplateBundle\Applicant\Converter;

class PostToApplicantDataConverter
{
    /**
     * @param \WP_Post $post
     * @return array
     */
    public static function convert (\WP_Post $post)
    {
        $content = get_the_content($post->post_content, false, $post);
        $content = apply_filters( 'the_content', $content );
        $content = str_replace( ']]>', ']]&gt;', $content );

        $data = $post->to_array();
        unset($data['post_password']);
        $data['permalink'] = get_permalink($post);
        $data['post_content'] = $content;
        $data['post_meta'] = [];
        $meta = get_post_meta($post->ID);
        foreach ($meta as $key => $value) {
            $data['post_meta'][$key] = reset($value);
        }
        $author = get_userdata($data['post_author']);
        if ($author instanceof \WP_User) {
            $data['post_author'] = $author->to_array();
            unset($data['post_author']['user_pass']);
            unset($data['post_author']['user_activation_key']);
        }
        $data['post_comments'] = get_comments([
            'post_id' => $post->ID,
        ]);

        return $data;
    }
}