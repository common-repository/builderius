<?php

namespace Builderius\Bundle\GraphQLBundle\Type;

use Builderius\Bundle\GraphQLBundle\Config\GraphQLEnumTypeConfig;
use Builderius\Bundle\GraphQLBundle\Config\GraphQLEnumValueConfig;
use Builderius\GraphQL\Type\Definition\EnumType;

class ThumbnailSizeType extends EnumType
{
    /**
     * @inheritDoc
     */
    public function __construct()
    {
        $values = [
            'ORIGINAL' => [
                GraphQLEnumValueConfig::DESCRIPTION_FIELD => 'original size',
                GraphQLEnumValueConfig::VALUE_FIELD => 'original'
            ]
        ];
        foreach (wp_get_registered_image_subsizes() as $name => $attr) {
            $formattedName = strtoupper(str_replace('-', '_', $name));
            if ($name === sprintf('%sx%s', $attr['width'], $attr['height'])) {
                $formattedName = sprintf('SIZE_%s', $formattedName);
            }
            $values[$formattedName] = [
                GraphQLEnumValueConfig::DESCRIPTION_FIELD => sprintf('%sx%s', $attr['width'], $attr['height']),
                GraphQLEnumValueConfig::VALUE_FIELD => $name
            ];
        }

        $params = [
            GraphQLEnumTypeConfig::NAME_FIELD => 'ThumbnailSize',
            GraphQLEnumTypeConfig::DESCRIPTION_FIELD => __('Registered Thumbnail Sizes'),
            GraphQLEnumTypeConfig::VALUES_FIELD => $values
        ];

        parent::__construct($params);
    }
}