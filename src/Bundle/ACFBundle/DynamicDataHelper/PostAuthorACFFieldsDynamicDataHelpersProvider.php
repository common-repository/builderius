<?php

namespace Builderius\Bundle\ACFBundle\DynamicDataHelper;

use Builderius\Bundle\TemplateBundle\DynamicDataHelper\DynamicDataHelper;
use BuilderiusPro\Bundle\GraphQLBundle\Resolver\ExpressionResultResolver;

class PostAuthorACFFieldsDynamicDataHelpersProvider extends AbstractACFFieldsDynamicDataHelpersProvider
{
    private function getDefaultParams(array $field, $idx)
    {
        $sanitizedName = preg_replace('/[^\\w]+/', '_', $field['name'], -1);
        if (!isset($field['return_format']) || $field['return_format'] !== 'array') {
            $expression = '"wp.post.author.acf_field__' . $sanitizedName . '"';
        } else {
            $expression = '"wp.post.author.acf_field__' . $sanitizedName . '." ~ argument';
        }
        return [
            DynamicDataHelper::NAME_FIELD => 'post_author_acf_value_' . $sanitizedName,
            DynamicDataHelper::LABEL_FIELD => 'Post author ACF "' . $field['label'] . '" field value',
            DynamicDataHelper::CATEGORY_FIELD => 'post_author',
            DynamicDataHelper::SORT_ORDER_FIELD => 300 + 10 * $idx,
            DynamicDataHelper::GRAPHQL_PATH_FIELD => [
                '"query.post.__aliasFor"' => '"queried_post"',
                '"query.post.author.__aliasFor"' => '"post_author"',
                '"query.post.author.acf_field__' . $sanitizedName. '.__aliasFor"' => '"acf_value"',
                '"query.post.author.acf_field__' . $sanitizedName. '.__args.name"' => '"' . $field['name'] . '"',
            ],
            DynamicDataHelper::EXPRESSION_FIELD => $expression,
            DynamicDataHelper::ESCAPED_FIELD => false
        ];
    }

    /**
     * @inheritDoc
     */
    public function getDynamicDataHelpers()
    {
        if (function_exists('acf_get_field_groups')) {
            $acf_field_groups = acf_get_field_groups();
            foreach ($acf_field_groups as $acf_field_group) {
                $applicable = false;
                foreach ($acf_field_group['location'] as $group_locations) {
                    foreach ($group_locations as $rule) {
                        if (in_array($rule['param'], ['current_user', 'current_user_role', 'user_form', 'user_role'])) {
                            $applicable = true;
                            break;
                        }
                    }
                    if ($applicable === true) {
                        break;
                    }
                }
                if ($applicable === true) {
                    $fields = acf_get_fields($acf_field_group);
                    foreach ($fields as $k => $field) {
                        if ($field['type'] === 'image') {
                            $this->addImageFieldHelper($field, $k);
                        } elseif ($field['type'] === 'file') {
                            $this->addFileFieldHelper($field, $k);
                        } elseif ($field['type'] === 'color_picker') {
                            $this->addColorPickerHelper($field, $k);
                        } elseif ($field['type'] === 'link') {
                            $this->addLinkFieldHelper($field, $k);
                        } elseif ($field['type'] === 'google_map') {
                            $this->addGoogleMapFieldHelper($field, $k);
                        } elseif ($field['type'] === 'group') {
                            $this->addGroupFieldHelper($field, $k);
                        } elseif ($field['type'] === 'flexible_content' && class_exists(ExpressionResultResolver::class)) {
                            $this->addFlexibleContentFieldHelper($field, $k);
                        } elseif ($field['type'] === 'post_object' && $field['multiple'] === 0) {
                            $this->addPostObjectFieldHelper($field, $k);
                        } elseif ($field['type'] === 'user' && $field['multiple'] === 0) {
                            $this->addUserFieldHelper($field, $k);
                        } elseif (in_array($field['type'], self::ARRAY_TYPES)) {
                            $this->addArrayFieldHelper($field, $k);
                        } else {
                            $this->addScalarFieldHelper($field, $k);
                        }
                    }
                }
            }
        }

        return $this->ddHelpers;
    }

    /**
     * @param array $field
     * @param $idx
     * @return void
     */
    private function addScalarFieldHelper(array $field, $idx)
    {
        $params = $this->getDefaultParams($field, $idx);
        $sanitizedName = preg_replace('/[^\\w]+/', '_', $field['name'], -1);
        $params[DynamicDataHelper::EXPRESSION_FIELD] = '"wp.post.author.acf_field__' . $sanitizedName . '"';
        $this->ddHelpers[$params[DynamicDataHelper::NAME_FIELD]] = $this->createHelper($params);
    }

    /**
     * @param array $field
     * @param $idx
     * @return void
     */
    private function addArrayFieldHelper(array $field, $idx)
    {
        $params = $this->getDefaultParams($field, $idx);
        $sanitizedName = preg_replace('/[^\\w]+/', '_', $field['name'], -1);
        $params[DynamicDataHelper::EXPRESSION_FIELD] = '"wp.post.author.acf_field__' . $sanitizedName . '"';
        $ddHelper = $this->createHelper($params);
        $ddHelper->setType('array');
        $this->ddHelpers[$params[DynamicDataHelper::NAME_FIELD]] = $ddHelper;
    }

    /**
     * @param array $field
     * @param $idx
     * @return void
     */
    private function addImageFieldHelper(array $field, $idx)
    {
        $params = $this->getDefaultParams($field, $idx);
        $this->ddHelpers[$params[DynamicDataHelper::NAME_FIELD]] = $this->createImageFieldHelper($field, $params);
    }

    /**
     * @param array $field
     * @param $idx
     * @return void
     */
    private function addFileFieldHelper(array $field, $idx)
    {
        $params = $this->getDefaultParams($field, $idx);
        $this->ddHelpers[$params[DynamicDataHelper::NAME_FIELD]] = $this->createFileFieldHelper($field, $params);
    }

    /**
     * @param array $field
     * @param $idx
     * @return void
     */
    private function addColorPickerHelper(array $field, $idx)
    {
        $params = $this->getDefaultParams($field, $idx);
        $this->ddHelpers[$params[DynamicDataHelper::NAME_FIELD]] = $this->createColorPickerFieldHelper($field, $params);
    }

    /**
     * @param array $field
     * @param $idx
     * @return void
     */
    private function addLinkFieldHelper(array $field, $idx)
    {
        $params = $this->getDefaultParams($field, $idx);
        $this->ddHelpers[$params[DynamicDataHelper::NAME_FIELD]] = $this->createLinkFieldHelper($field, $params);
    }

    /**
     * @param array $field
     * @param $idx
     * @return void
     */
    private function addGoogleMapFieldHelper(array $field, $idx)
    {
        $params = $this->getDefaultParams($field, $idx);
        $sanitizedName = preg_replace('/[^\\w]+/', '_', $field['name'], -1);
        $params[DynamicDataHelper::EXPRESSION_FIELD] = '"wp.post.author.acf_field__' . $sanitizedName . '." ~ argument';
        $this->ddHelpers[$params[DynamicDataHelper::NAME_FIELD]] = $this->createGoogleMapFieldHelper($params);
    }

    /**
     * @param array $field
     * @param $idx
     * @return void
     */
    private function addGroupFieldHelper(array $field, $idx)
    {
        $params = $this->getDefaultParams($field, $idx);
        $sanitizedName = preg_replace('/[^\\w]+/', '_', $field['name'], -1);
        $params[DynamicDataHelper::EXPRESSION_FIELD] = '"wp.post.author.acf_field__' . $sanitizedName . '." ~ argument';
        $this->ddHelpers[$params[DynamicDataHelper::NAME_FIELD]] = $this->createGroupFieldHelper($field, $params);
    }

    /**
     * @param array $field
     * @param $idx
     * @return void
     */
    private function addFlexibleContentFieldHelper(array $field, $idx)
    {
        $params = $this->getDefaultParams($field, $idx);
        $sanitizedName = preg_replace('/[^\\w]+/', '_', $field['name'], -1);
        $params[DynamicDataHelper::GRAPHQL_PATH_FIELD] = [
            '"query.post.__aliasFor"' => '"queried_post"',
            '"query.post.author.__aliasFor"' => '"post_author"',
            '"query.post.author.acf_field__' . $sanitizedName. '_private.__aliasFor"' => '"acf_value"',
            '"query.post.author.acf_field__' . $sanitizedName. '_private.__args.name"' => '"' . $field['name'] . '"',
            '"query.post.author.acf_field__' . $sanitizedName. '_private.__directives"' => '{"private": true}',
            '"query.post.author.acf_field__' . $sanitizedName. '.__aliasFor"' => '"expression_result"',
            '"query.post.author.acf_field__' . $sanitizedName. '.__args.expression"' => '"{{{foreach(group_by(acf_field__' . $sanitizedName. '_private, \'acf_fc_layout\'), (value) -> {value[0]}) }}}"',
        ];
        $params[DynamicDataHelper::EXPRESSION_FIELD] = '"wp.post.author.acf_field__' . $sanitizedName . '." ~ argument';
        $this->ddHelpers[$params[DynamicDataHelper::NAME_FIELD]] = $this->createFlexibleContentFieldHelper($field, $params);
    }

    /**
     * @param array $field
     * @param $idx
     * @return void
     */
    private function addPostObjectFieldHelper(array $field, $idx)
    {
        $params = $this->getDefaultParams($field, $idx);
        $sanitizedName = preg_replace('/[^\\w]+/', '_', $field['name'], -1);
        $params[DynamicDataHelper::GRAPHQL_PATH_FIELD] = [
            '"query.post.__aliasFor"' => '"queried_post"',
            '"query.post.author.__aliasFor"' => '"post_author"',
            '"query.post.author.acf_field__' . $sanitizedName. '.__aliasFor"' => '"acf_post_object_value"',
            '"query.post.author.acf_field__' . $sanitizedName. '.__args.name"' => '"' . $field['name'] . '"',
            '"query.post.author.acf_field__' . $sanitizedName. '." ~ (isset(config[argument], "parent_alias") ? config[argument].parent_alias : config[argument].alias) ~ ".__aliasFor"' => 'isset(config[argument], "parent_field") ? config[argument].parent_field : config[argument].field',
            '"query.post.author.acf_field__' . $sanitizedName. '." ~ (isset(config[argument], "parent_alias") ? config[argument].parent_alias ~ "." : "") ~ config[argument].alias ~ ".__aliasFor"' => 'config[argument].field',
        ];
        $params[DynamicDataHelper::EXPRESSION_FIELD] = '"wp.post.author.acf_field__' . $sanitizedName . '." ~ (isset(config[argument], "parent_alias") ? config[argument].parent_alias ~ "." : "") ~ config[argument].alias';
        $this->ddHelpers[$params[DynamicDataHelper::NAME_FIELD]] = $this->createPostObjectFieldHelper($params);
    }

    /**
     * @param array $field
     * @param $idx
     * @return void
     */
    private function addUserFieldHelper(array $field, $idx)
    {
        $params = $this->getDefaultParams($field, $idx);
        $sanitizedName = preg_replace('/[^\\w]+/', '_', $field['name'], -1);
        $params[DynamicDataHelper::GRAPHQL_PATH_FIELD] = [
            '"query.post.__aliasFor"' => '"queried_post"',
            '"query.post.author.__aliasFor"' => '"post_author"',
            '"query.post.author.acf_field__' . $sanitizedName. '.__aliasFor"' => '"acf_user_value"',
            '"query.post.author.acf_field__' . $sanitizedName. '.__args.name"' => '"' . $field['name'] . '"',
            '"query.post.author.acf_field__' . $sanitizedName. '." ~ config[argument].alias ~ ".__aliasFor"' => 'config[argument].field',
        ];
        $params[DynamicDataHelper::EXPRESSION_FIELD] = '"wp.post.author.acf_field__' . $sanitizedName . '." ~ config[argument].alias';
        $this->ddHelpers[$params[DynamicDataHelper::NAME_FIELD]] = $this->createUserFieldHelper($params);
    }
}