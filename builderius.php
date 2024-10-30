<?php

/**
 * Plugin Name: Builderius
 * Plugin URI: https://builderius.io
 * Description: Professional site builder for WordPress. Create astonishing designs with easy to use drag and drop interface.
 * Version: 0.15
 * Contributors: builderius, mrpsiho, vdenchyk
 * Author: Builderius
 * Author URI: https://builderius.io
 * Domain Path: /languages/
 * Text Domain: builderius
 * Requires at least: 5.4
 * Tested up to: 6.6
 * Requires PHP: 7.3
 * License: GPL v2.0 or later
 */
/**
 * Builderius
 * Copyright (C) 2021, Builderius - sales@builderius.io
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2.0 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 */

if (!defined('ABSPATH')) {
    exit;
    // Exit if accessed directly.
} elseif (strpos($_SERVER['PHP_SELF'], 'favicon.ico') !== false) {
    return;
}

$classes = get_declared_classes();
include __DIR__.'/vendor-prefixed/moomoo/plugin-platform/src/Bundle/KernelBundle/Kernel/Kernel.php';
$newClasses = get_declared_classes();
$diffClasses = array_diff($newClasses, $classes);
$kernelClass = null;
foreach ($diffClasses as $class) {
    if (strpos($class, '.php') === false &&
        strpos($class, 'MooMoo\Platform\Bundle\KernelBundle\Kernel\Kernel') !== false) {
        $kernelClass = $class;
        break;
    }
}
require_once("vendor-prefixed/scoper-autoload.php");

if (!version_compare(PHP_VERSION, '7.2', '>=')) {
    add_action('admin_notices', 'builderius_fail_php_version');
} elseif (!version_compare(get_bloginfo('version'), '5.4', '>=')) {
    add_action('admin_notices', 'builderius_fail_wp_version');
} else {
    if (isset($_GET['clear-builderius-cache'])) {
        $cacheDir = \sprintf('%s/builderius/cache', wp_upload_dir()['basedir']);
        $fs = new \Builderius\Symfony\Component\Filesystem\Filesystem();
        $fs->remove($cacheDir);
        $url = $_SERVER['REQUEST_URI'];
        $url = str_replace('?clear-builderius-cache', '', $url);
        $url = str_replace('&clear-builderius-cache', '', $url);
        header( sprintf("Location: %s", $url));
        exit ;
    }
    $builderiusDevelopmentMode = false;
    if (defined( 'BUILDERIUS_DEVELOPMENT_MODE') && BUILDERIUS_DEVELOPMENT_MODE) {
        $builderiusDevelopmentMode = true;
    }
    try {
        $kernel = new $kernelClass($builderiusDevelopmentMode);
        $kernel->boot();
    } catch (\Throwable $e) {
        if (strpos($e->getMessage(), '/builderius/cache') !== false) {
            $url = $_SERVER['REQUEST_URI'];
            if (!strpos($url, '?')) {
                $url = sprintf('%s?clear-builderius-cache', $url);
            } else {
                $url = sprintf('%s&clear-builderius-cache', $url);
            }
            add_action('admin_notices', 'builderius_cache_clear_notice');
            header( sprintf("Location: %s", $url));
            exit ;
        }
    }
}
/**
 * PHP version check notice
 *
 * @return void
 */
function builderius_cache_clear_notice()
{
    $url = $_SERVER['REQUEST_URI'];
    if (!strpos($url, '?')) {
        $url = sprintf('%s?clear-builderius-cache', $url);
    } else {
        $url = sprintf('%s&clear-builderius-cache', $url);
    }
    $message = sprintf(
        esc_html__("It seems Builderius cache should be re-created, please proceed this <a href='%s'>link text</a>link to do it", 'builderius'),
        $url
    );
    echo builderius_fail_notice_wrapper($message);
}

/**
 * PHP version check notice
 *
 * @return void
 */
function builderius_fail_php_version()
{
    $message = sprintf(
        esc_html__('Builderius requires PHP version %s+. It is NOT ACTIVATED!', 'builderius'),
        '7.2'
    );
    echo builderius_fail_notice_wrapper($message);
}

/**
 * WP version check notice
 *
 * @return void
 */
function builderius_fail_wp_version()
{
    $message = sprintf(
        esc_html__('Builderius requires WordPress version %s+. It is NOT ACTIVATED!', 'builderius'),
        '5.4'
    );
    echo builderius_fail_notice_wrapper($message);
}

/**
 * Fail notices wrapper. Returns sanitized string.
 *
 * @param string
 *
 * @return string
 */
function builderius_fail_notice_wrapper($message)
{
    $html_message = sprintf('<div class="error">%s</div>', wpautop($message));
    return wp_kses_post($html_message);
}
