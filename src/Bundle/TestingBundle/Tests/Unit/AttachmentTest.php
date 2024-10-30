<?php

namespace Builderius\Bundle\TestingBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;

class AttachmentTest extends TestCase
{
    public function testId()
    {
        $url = 'woocommerce-placeholder-300x300.png';
        $id = null;
        require __DIR__ . '/../../../../../../../../wp-load.php';
        $id = attachment_url_to_postid($url);
        if (0 === $id) {
            global $wpdb;

            $dir  = wp_get_upload_dir();
            $path = $url;

            $site_url   = parse_url( $dir['url'] );
            $image_path = parse_url( $path );

            // Force the protocols to match if needed.
            if ( isset( $image_path['scheme'] ) && ( $image_path['scheme'] !== $site_url['scheme'] ) ) {
                $path = str_replace( $image_path['scheme'], $site_url['scheme'], $path );
            }

            if ( 0 === strpos( $path, $dir['baseurl'] . '/' ) ) {
                $path = substr( $path, strlen( $dir['baseurl'] . '/' ) );
            }
            $sql = sprintf(
                "SELECT post_id, meta_value FROM %s WHERE meta_key = '_wp_attachment_metadata' AND meta_value LIKE '%%%s%%'",
                $wpdb->postmeta,
                $path
            );

            $results = $wpdb->get_results( $sql );
            if ( $results ) {
                $id = reset( $results )->post_id;
            }
        }
        if ($id) {

        }
        add_image_size();
        wp_generate_attachment_metadata();
        $meta = wp_get_attachment_metadata($id);
        $test = $id;
    }
}