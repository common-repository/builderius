<?php

namespace Builderius\Bundle\ACFBundle\DynamicDataHelper;

use Builderius\Bundle\GraphQLBundle\Config\GraphQLEnumValueConfig;
use Builderius\Bundle\TemplateBundle\DynamicDataHelper\DynamicDataHelper;
use Builderius\Bundle\TemplateBundle\DynamicDataHelper\DynamicDataHelperArgument;
use Builderius\Bundle\TemplateBundle\DynamicDataHelper\DynamicDataHelperInterface;
use Builderius\Bundle\TemplateBundle\DynamicDataHelper\DynamicDataHelpersProviderInterface;

abstract class AbstractACFFieldsDynamicDataHelpersProvider implements DynamicDataHelpersProviderInterface
{
    CONST SCALAR_TYPES = [
        'text', 'textarea', 'number', 'range', 'email', 'url', 'password', 'wysiwyg', 'oembed', 'select', 'radio',
        'button_group', 'true_false', 'page_link', 'date_picker', 'date_time_picker', 'time_picker',

    ];
    CONST ARRAY_TYPES = [
        'repeater', 'gallery', 'checkbox', 'relationship'
    ];

    /**
     * @var DynamicDataHelperInterface
     */
    protected $ddHelpers = [];

    /**
     * @param array $params
     * @return DynamicDataHelper
     */
    protected function createHelper(array $params)
    {
        return new DynamicDataHelper($params);
    }

    /**
     * @param array $field
     * @param array $params
     * @return DynamicDataHelper
     */
    protected function createImageFieldHelper(
        array $field,
        array $params
    ) {
        $ddHelper = $this->createHelper($params);
        if (isset($field['return_format']) && $field['return_format'] == 'array') {
            $argument = new DynamicDataHelperArgument([
                DynamicDataHelperArgument::NAME_FIELD => 'argument',
                DynamicDataHelperArgument::TYPE_FIELD => 'select',
                DynamicDataHelperArgument::PLACEHOLDER_FIELD => __('Please select subfield'),
                DynamicDataHelperArgument::VALUE_LIST_FIELD => [
                    ['value' => 'alt', 'title' => 'alt'],
                    ['value' => 'author', 'title' => 'author'],
                    ['value' => 'caption', 'title' => 'caption'],
                    ['value' => 'date', 'title' => 'date'],
                    ['value' => 'description', 'title' => 'description'],
                    ['value' => 'filename', 'title' => 'filename'],
                    ['value' => 'filesize', 'title' => 'filesize'],
                    ['value' => 'height', 'title' => 'height'],
                    ['value' => 'icon', 'title' => 'icon'],
                    ['value' => 'ID', 'title' => 'ID'],
                    ['value' => 'id', 'title' => 'id'],
                    ['value' => 'link', 'title' => 'link'],
                    ['value' => 'menu_order', 'title' => 'menu_order'],
                    ['value' => 'mime_type', 'title' => 'mime_type'],
                    ['value' => 'modified', 'title' => 'modified'],
                    ['value' => 'name', 'title' => 'name'],
                    ['value' => 'sizes', 'title' => 'sizes'],
                    ['value' => 'status', 'title' => 'status'],
                    ['value' => 'subtype', 'title' => 'subtype'],
                    ['value' => 'title', 'title' => 'title'],
                    ['value' => 'type', 'title' => 'type'],
                    ['value' => 'uploaded_to', 'title' => 'uploaded_to'],
                    ['value' => 'url', 'title' => 'url'],
                    ['value' => 'width', 'title' => 'width'],
                ]
            ]);
            $ddHelper->addArgument($argument);
        }

        return $ddHelper;
    }

    /**
     * @param array $field
     * @param array $params
     * @return DynamicDataHelper
     */
    protected function createFileFieldHelper(
        array $field,
        array $params
    ) {
        $ddHelper = $this->createHelper($params);
        if (isset($field['return_format']) && $field['return_format'] == 'array') {
            $argument = new DynamicDataHelperArgument([
                DynamicDataHelperArgument::NAME_FIELD => 'argument',
                DynamicDataHelperArgument::TYPE_FIELD => 'select',
                DynamicDataHelperArgument::PLACEHOLDER_FIELD => __('Please select subfield'),
                DynamicDataHelperArgument::VALUE_LIST_FIELD => [
                    ['value' => 'alt', 'title' => 'alt'],
                    ['value' => 'author', 'title' => 'author'],
                    ['value' => 'caption', 'title' => 'caption'],
                    ['value' => 'date', 'title' => 'date'],
                    ['value' => 'description', 'title' => 'description'],
                    ['value' => 'filename', 'title' => 'filename'],
                    ['value' => 'filesize', 'title' => 'filesize'],
                    ['value' => 'icon', 'title' => 'icon'],
                    ['value' => 'ID', 'title' => 'ID'],
                    ['value' => 'id', 'title' => 'id'],
                    ['value' => 'link', 'title' => 'link'],
                    ['value' => 'menu_order', 'title' => 'menu_order'],
                    ['value' => 'mime_type', 'title' => 'mime_type'],
                    ['value' => 'modified', 'title' => 'modified'],
                    ['value' => 'name', 'title' => 'name'],
                    ['value' => 'status', 'title' => 'status'],
                    ['value' => 'subtype', 'title' => 'subtype'],
                    ['value' => 'title', 'title' => 'title'],
                    ['value' => 'type', 'title' => 'type'],
                    ['value' => 'uploaded_to', 'title' => 'uploaded_to'],
                    ['value' => 'url', 'title' => 'url'],
                ]
            ]);
            $ddHelper->addArgument($argument);
        }
        
        return $ddHelper;
    }

    /**
     * @param array $field
     * @param array $params
     * @return DynamicDataHelper
     */
    protected function createColorPickerFieldHelper(
        array $field,
        array $params
    ) {
        $ddHelper = $this->createHelper($params);
        if (isset($field['return_format']) && $field['return_format'] == 'array') {
            $argument = new DynamicDataHelperArgument([
                DynamicDataHelperArgument::NAME_FIELD => 'argument',
                DynamicDataHelperArgument::TYPE_FIELD => 'select',
                DynamicDataHelperArgument::PLACEHOLDER_FIELD => __('Please select subfield'),
                DynamicDataHelperArgument::VALUE_LIST_FIELD => [
                    ['value' => 'alpha', 'title' => 'alpha'],
                    ['value' => 'blue', 'title' => 'blue'],
                    ['value' => 'green', 'title' => 'green'],
                    ['value' => 'red', 'title' => 'red']
                ]
            ]);
            $ddHelper->addArgument($argument);
        }
        
        return $ddHelper;
    }

    /**
     * @param array $field
     * @param array $params
     * @return DynamicDataHelper
     */
    protected function createLinkFieldHelper(
        array $field,
        array $params
    ) {
        $ddHelper = $this->createHelper($params);
        if (isset($field['return_format']) && $field['return_format'] == 'array') {
            $argument = new DynamicDataHelperArgument([
                DynamicDataHelperArgument::NAME_FIELD => 'argument',
                DynamicDataHelperArgument::TYPE_FIELD => 'select',
                DynamicDataHelperArgument::PLACEHOLDER_FIELD => __('Please select subfield'),
                DynamicDataHelperArgument::VALUE_LIST_FIELD => [
                    ['value' => 'title', 'title' => 'title'],
                    ['value' => 'url', 'title' => 'URL'],
                    ['value' => 'target', 'title' => 'target']
                ]
            ]);
            $ddHelper->addArgument($argument);
        }

        return $ddHelper;
    }

    /**
     * @param array $params
     * @return DynamicDataHelper
     */
    protected function createGoogleMapFieldHelper(
        array $params
    ) {
        $ddHelper = $this->createHelper($params);
        $argument = new DynamicDataHelperArgument([
            DynamicDataHelperArgument::NAME_FIELD => 'argument',
            DynamicDataHelperArgument::TYPE_FIELD => 'select',
            DynamicDataHelperArgument::PLACEHOLDER_FIELD => __('Please select subfield'),
            DynamicDataHelperArgument::VALUE_LIST_FIELD => [
                ['value' => 'address', 'title' => 'address'],
                ['value' => 'city', 'title' => 'city'],
                ['value' => 'country', 'title' => 'country'],
                ['value' => 'country_short', 'title' => 'country_short'],
                ['value' => 'lat', 'title' => 'lat'],
                ['value' => 'lng', 'title' => 'lng'],
                ['value' => 'name', 'title' => 'name'],
                ['value' => 'place_id', 'title' => 'place_id'],
                ['value' => 'post_code', 'title' => 'post_code'],
                ['value' => 'state', 'title' => 'state'],
                ['value' => 'street_name', 'title' => 'street_name'],
                ['value' => 'street_name_short', 'title' => 'street_name_short'],
                ['value' => 'street_number', 'title' => 'street_number'],
                ['value' => 'zoom', 'title' => 'zoom']
            ]
        ]);
        $ddHelper->addArgument($argument);

        return $ddHelper;
    }

    /**
     * @param array $field
     * @param array $params
     * @return DynamicDataHelper
     */
    protected function createGroupFieldHelper(
        array $field,
        array $params
    ) {
        $subFields = acf_get_fields($field);
        $valueList = [];
        foreach ($subFields as $subField) {
            if (
                in_array($subField['type'], self::SCALAR_TYPES) ||
                (
                    in_array($subField['type'], ['image', 'file', 'link', 'color_picker']) &&
                    (
                        !isset($field['return_format']) || $subField['return_format'] !== 'array'
                    )
                )
            ) {
                $valueList[] = ['value' => $subField['name'], 'title' => $subField['label']];
            }
        }

        $ddHelper = $this->createHelper($params);
        $argument = new DynamicDataHelperArgument([
            DynamicDataHelperArgument::NAME_FIELD => 'argument',
            DynamicDataHelperArgument::TYPE_FIELD => 'select',
            DynamicDataHelperArgument::PLACEHOLDER_FIELD => __('Please select subfield'),
            DynamicDataHelperArgument::VALUE_LIST_FIELD => $valueList
        ]);
        $ddHelper->addArgument($argument);

        return $ddHelper;
    }

    /**
     * @param array $field
     * @param array $params
     * @return DynamicDataHelper
     */
    protected function createFlexibleContentFieldHelper(
        array $field,
        array $params
    ) {
        $layouts = $field['layouts'];
        $subFields = acf_get_fields($field);
        $valueList = [];
        foreach ($subFields as $subField) {
            if (
                in_array($subField['type'], self::SCALAR_TYPES) ||
                (in_array($subField['type'], ['image', 'file', 'link', 'color_picker']) && $subField['return_format'] !== 'array')
            ) {
                $valueList[] = ['value' => $layouts[$subField['parent_layout']]['name'] . '.' . $subField['name'], 'title' => $layouts[$subField['parent_layout']]['label'] . ' - ' . $subField['label']];
            }
        }

        $ddHelper = $this->createHelper($params);
        $argument = new DynamicDataHelperArgument([
            DynamicDataHelperArgument::NAME_FIELD => 'argument',
            DynamicDataHelperArgument::TYPE_FIELD => 'select',
            DynamicDataHelperArgument::PLACEHOLDER_FIELD => __('Please select subfield'),
            DynamicDataHelperArgument::VALUE_LIST_FIELD => $valueList
        ]);
        $ddHelper->addArgument($argument);

        return $ddHelper;
    }

    /**
     * @param array $params
     * @return DynamicDataHelper
     */
    protected function createPostObjectFieldHelper(
        array $params
    ) {
        $ddHelper = $this->createHelper($params);
        $argument = new DynamicDataHelperArgument([
            DynamicDataHelperArgument::NAME_FIELD => 'argument',
            DynamicDataHelperArgument::TYPE_FIELD => 'select',
            DynamicDataHelperArgument::PLACEHOLDER_FIELD => __('Please select subfield'),
            DynamicDataHelperArgument::VALUE_LIST_FIELD => [
                ['value' => 'post_title', 'title' => 'Post title'],
                ['value' => 'ID', 'title' => 'Post ID'],
                ['value' => 'guid', 'title' => 'Post link'],
                ['value' => 'post_date', 'title' => 'Post datetime'],
                ['value' => 'post_modified', 'title' => 'Post modified datetime'],
                ['value' => 'post_content', 'title' => 'Post content'],
                ['value' => 'post_excerpt', 'title' => 'Post excerpt'],
                ['value' => 'post_status', 'title' => 'Post status'],
                ['value' => 'post_type', 'title' => 'Post type'],
                ['value' => 'post_author.user_login', 'title' => 'Author name'],
                ['value' => 'post_author.ID', 'title' => 'Author ID'],
                ['value' => 'post_author.display_name', 'title' => 'Author display name'],
                ['value' => 'post_author.first_name', 'title' => 'Author first name'],
                ['value' => 'post_author.last_name', 'title' => 'Author last name'],
                ['value' => 'post_author.user_email', 'title' => 'Author email'],
                ['value' => 'post_author.avatar_url', 'title' => 'Author avatar'],
            ]
        ]);
        $ddHelper->addArgument($argument);
        $argument = new DynamicDataHelperArgument([
            DynamicDataHelperArgument::NAME_FIELD => 'config',
            DynamicDataHelperArgument::TYPE_FIELD => 'hidden',
            DynamicDataHelperArgument::VALUE_FIELD => [
                'post_title' => ['alias' => 'title', 'field' => 'post_title'],
                'ID' => ['alias' => 'ID', 'field' => 'ID'],
                'guid' => ['alias' => 'link', 'field' => 'guid'],
                'post_date' => ['alias' => 'datetime', 'field' => 'post_date'],
                'post_modified' => ['alias' => 'modified_datetime', 'field' => 'post_modified'],
                'post_content' => ['alias' => 'content', 'field' => 'post_content'],
                'post_excerpt' => ['alias' => 'excerpt', 'field' => 'post_excerpt'],
                'post_status' => ['alias' => 'status', 'field' => 'post_status'],
                'post_type' => ['alias' => 'type', 'field' => 'post_type'],
                'post_author.user_login' => ['alias' => 'name', 'field' => 'user_login', 'parent_alias' => 'author', 'parent_field' => 'post_author'],
                'post_author.ID' => ['alias' => 'ID', 'field' => 'ID', 'parent_alias' => 'author', 'parent_field' => 'post_author'],
                'post_author.display_name' => ['alias' => 'display_name', 'field' => 'display_name', 'parent_alias' => 'author', 'parent_field' => 'post_author'],
                'post_author.first_name' => ['alias' => 'first_name', 'field' => 'first_name', 'parent_alias' => 'author', 'parent_field' => 'post_author'],
                'post_author.last_name' => ['alias' => 'last_name', 'field' => 'last_name', 'parent_alias' => 'author', 'parent_field' => 'post_author'],
                'post_author.user_email' => ['alias' => 'email', 'field' => 'user_email', 'parent_alias' => 'author', 'parent_field' => 'post_author'],
                'post_author.avatar_url' => ['alias' => 'avatar', 'field' => 'avatar_url', 'parent_alias' => 'author', 'parent_field' => 'post_author'],
            ]
        ]);
        $ddHelper->addArgument($argument);

        return $ddHelper;
    }

    /**
     * @param array $params
     * @return DynamicDataHelper
     */
    protected function createUserFieldHelper(
        array $params
    ) {
        $ddHelper = $this->createHelper($params);
        $argument = new DynamicDataHelperArgument([
            DynamicDataHelperArgument::NAME_FIELD => 'argument',
            DynamicDataHelperArgument::TYPE_FIELD => 'select',
            DynamicDataHelperArgument::PLACEHOLDER_FIELD => __('Please select subfield'),
            DynamicDataHelperArgument::VALUE_LIST_FIELD => [
                ['value' => 'user_login', 'title' => 'User name'],
                ['value' => 'ID', 'title' => 'User ID'],
                ['value' => 'display_name', 'title' => 'User display name'],
                ['value' => 'first_name', 'title' => 'User first name'],
                ['value' => 'last_name', 'title' => 'User last name'],
                ['value' => 'user_email', 'title' => 'User email'],
                ['value' => 'avatar_url', 'title' => 'User avatar']
            ]
        ]);
        $ddHelper->addArgument($argument);
        $argument = new DynamicDataHelperArgument([
            DynamicDataHelperArgument::NAME_FIELD => 'config',
            DynamicDataHelperArgument::TYPE_FIELD => 'hidden',
            DynamicDataHelperArgument::VALUE_FIELD => [
                'user_login' => ['alias' => 'name', 'field' => 'user_login'],
                'ID' => ['alias' => 'ID', 'field' => 'ID'],
                'display_name' => ['alias' => 'display_name', 'field' => 'display_name'],
                'first_name' => ['alias' => 'first_name', 'field' => 'first_name'],
                'last_name' => ['alias' => 'last_name', 'field' => 'last_name'],
                'user_email' => ['alias' => 'email', 'field' => 'user_email'],
                'avatar_url' => ['alias' => 'avatar', 'field' => 'avatar_url']
            ]
        ]);
        $ddHelper->addArgument($argument);

        return $ddHelper;
    }

    /**
     * @inheritDoc
     */
    public function getDynamicDataHelper($name)
    {
        if ($this->hasDynamicDataHelper($name)) {
            return $this->ddHelpers[$name];
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function hasDynamicDataHelper($name)
    {
        return array_key_exists($name, $this->ddHelpers);
    }
}