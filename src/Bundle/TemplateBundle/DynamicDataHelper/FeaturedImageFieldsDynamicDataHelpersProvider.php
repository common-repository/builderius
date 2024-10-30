<?php

namespace Builderius\Bundle\TemplateBundle\DynamicDataHelper;

class FeaturedImageFieldsDynamicDataHelpersProvider implements DynamicDataHelpersProviderInterface
{
    /**
     * @var DynamicDataHelperInterface
     */
    private $ddHelpers = [];

    /**
     * @inheritDoc
     */
    public function getDynamicDataHelpers()
    {
        if (empty($this->ddHelpers)) {
            $this->ddHelpers['featured_image_alt_text'] = new DynamicDataHelper([
                DynamicDataHelper::NAME_FIELD => 'featured_image_alt_text',
                DynamicDataHelper::LABEL_FIELD => 'Featured image alt text',
                DynamicDataHelper::SORT_ORDER_FIELD => 10,
                DynamicDataHelper::CATEGORY_FIELD => 'featured_image',
                DynamicDataHelper::GRAPHQL_PATH_FIELD => [
                    '"query.post.__aliasFor"' => '"queried_post"',
                    '"query.post.featured_image.alt_text"' => true,
                ],
                DynamicDataHelper::EXPRESSION_FIELD => '"wp.post.featured_image.alt_text"'
            ]);
            $this->ddHelpers['featured_image_caption'] = new DynamicDataHelper([
                DynamicDataHelper::NAME_FIELD => 'featured_image_caption',
                DynamicDataHelper::LABEL_FIELD => 'Featured image caption',
                DynamicDataHelper::SORT_ORDER_FIELD => 20,
                DynamicDataHelper::CATEGORY_FIELD => 'featured_image',
                DynamicDataHelper::GRAPHQL_PATH_FIELD => [
                    '"query.post.__aliasFor"' => '"queried_post"',
                    '"query.post.featured_image.caption"' => true,
                ],
                DynamicDataHelper::EXPRESSION_FIELD => '"wp.post.featured_image.caption"'
            ]);

            $argsValueList = [
                ['value' => 'ORIGINAL', 'title' => 'Original']
            ];
            foreach (wp_get_registered_image_subsizes() as $name => $attr) {
                $formattedLabel = ucfirst(str_replace('-', ' ', str_replace('_', ' ', $name)));
                $formattedAttr = strtoupper(str_replace('-', '_', $name));
                if ($name === sprintf('%sx%s', $attr['width'], $attr['height'])) {
                    $formattedAttr = sprintf('SIZE_%s', $formattedAttr);
                    $formattedLabel = sprintf('%spx', $formattedLabel);
                }
                $argsValueList[] = ['value' => $formattedAttr, 'title' => $formattedLabel];
            }
            $this->ddHelpers['featured_image_file_url'] = new DynamicDataHelper([
                DynamicDataHelper::NAME_FIELD => 'featured_image_file_url',
                DynamicDataHelper::LABEL_FIELD => 'Featured image file URL',
                DynamicDataHelper::SORT_ORDER_FIELD => 30,
                DynamicDataHelper::CATEGORY_FIELD => 'featured_image',
                DynamicDataHelper::ESCAPED_FIELD => false,
                DynamicDataHelper::GRAPHQL_PATH_FIELD => [
                    "'query.post.__aliasFor'" => "'queried_post'",
                    "'query.post.featured_image.file_url__' ~ lower(argument) ~ '.__aliasFor'" => "'file_url'",
                    "'query.post.featured_image.file_url__' ~ lower(argument) ~ '.__args.size'" => 'argument'
                ],
                DynamicDataHelper::EXPRESSION_FIELD => "'wp.post.featured_image.file_url__' ~ lower(argument)",
                DynamicDataHelper::ARGUMENTS_FIELD => [
                    'argument' => new DynamicDataHelperArgument([
                        DynamicDataHelperArgument::NAME_FIELD => 'argument',
                        DynamicDataHelperArgument::TYPE_FIELD => 'select',
                        DynamicDataHelperArgument::PLACEHOLDER_FIELD => __('Image size'),
                        DynamicDataHelperArgument::VALUE_LIST_FIELD => $argsValueList,
                        DynamicDataHelperArgument::ENUM_FIELD => true
                    ])
                ]
            ]);
        }

        return $this->ddHelpers;
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