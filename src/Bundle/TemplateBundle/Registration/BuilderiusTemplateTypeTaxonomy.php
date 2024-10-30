<?php

namespace Builderius\Bundle\TemplateBundle\Registration;

use Builderius\Bundle\TemplateBundle\Provider\TemplateType\BuilderiusTemplateTypesProviderInterface;
use Builderius\MooMoo\Platform\Bundle\TaxonomyBundle\Model\AbstractTaxonomy;
use Builderius\MooMoo\Platform\Bundle\TaxonomyBundle\Model\Term;

class BuilderiusTemplateTypeTaxonomy extends AbstractTaxonomy
{
    const NAME = 'builderius_template_type';

    /**
     * @var BuilderiusTemplateTypesProviderInterface
     */
    private $templateTypesProvider;

    /**
     * @param BuilderiusTemplateTypesProviderInterface $templateTypesProvider
     */
    public function __construct(BuilderiusTemplateTypesProviderInterface $templateTypesProvider)
    {
        $this->templateTypesProvider = $templateTypesProvider;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * @inheritDoc
     */
    public function getObjectType()
    {
        return BuilderiusTemplatePostType::POST_TYPE;
    }

    /**
     * @inheritDoc
     */
    public function getArguments()
    {
        return [
            'hierarchical' => false,
            'show_ui' => false,
            'show_in_nav_menus' => false,
            'show_admin_column' => false,
            'show_in_rest' => false,
            'query_var' => is_admin(),
            'rewrite' => false,
            'public' => false,
        ];
    }

    /**
     * @inheritDoc
     */
    public function getTerms()
    {
        $terms = parent::getTerms();
        foreach ($this->templateTypesProvider->getTypes() as $type) {
            $typeName = $type->getName();
            if (!array_key_exists($typeName, $terms)) {
                $terms[$typeName] = new Term(
                    [
                        Term::NAME_FIELD => $typeName,
                        Term::SLUG_FIELD => $typeName,
                        Term::TAXONOMY_FIELD => self::NAME
                    ]
                );
            }
        }

        return $terms;
    }
}
