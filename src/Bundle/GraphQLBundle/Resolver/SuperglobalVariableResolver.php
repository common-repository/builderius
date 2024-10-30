<?php

namespace Builderius\Bundle\GraphQLBundle\Resolver;

use Builderius\GraphQL\Type\Definition\ResolveInfo;

class SuperglobalVariableResolver extends AbstractLocalVarsAwareResolver
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
        return 'superglobal_variable';
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
        $args = $this->processArguments($args, $info->path);
        $key = $args['key'];
        $fallback = isset($args['fallback']) ? $args['fallback'] : null;
        switch ($args['variable']) {
            case 'SERVER':
                return array_key_exists($key, $_SERVER) ? (is_string($_SERVER[$key]) ? sanitize_text_field($_SERVER[$key]) : $_SERVER[$key]) : $fallback;
            case 'GET':
                return array_key_exists($key, $_GET) ? (is_string($_GET[$key]) ? sanitize_text_field($_GET[$key]) : $_GET[$key]) : $fallback;
            case 'POST':
                return array_key_exists($key, $_POST) ? (is_string($_POST[$key]) ? sanitize_text_field($_POST[$key]) : $_POST[$key]) : $fallback;
            case 'FILES':
                return array_key_exists($key, $_FILES) ? (is_string($_FILES[$key]) ? sanitize_text_field($_FILES[$key]) : $_FILES[$key]) : $fallback;
            case 'REQUEST':
                return array_key_exists($key, $_REQUEST) ? (is_string($_REQUEST[$key]) ? sanitize_text_field($_REQUEST[$key]) : $_REQUEST[$key]) : $fallback;
            case 'SESSION':
                return array_key_exists($key, $_SESSION) ? (is_string($_SESSION[$key]) ? sanitize_text_field($_SESSION[$key]) : $_SESSION[$key]) : $fallback;
            case 'ENV':
                return array_key_exists($key, $_ENV) ? (is_string($_ENV[$key]) ? sanitize_text_field($_ENV[$key]) : $_ENV[$key]) : $fallback;
            case 'COOKIE':
                return array_key_exists($key, $_COOKIE) ? (is_string($_COOKIE[$key]) ? sanitize_text_field($_COOKIE[$key]) : $_COOKIE[$key]) : $fallback;
            default:
                return $fallback;
        }
    }
}