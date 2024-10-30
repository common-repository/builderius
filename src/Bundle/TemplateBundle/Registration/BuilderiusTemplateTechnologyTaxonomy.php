<?php

namespace Builderius\Bundle\TemplateBundle\Registration;

use Builderius\Bundle\TemplateBundle\Provider\TemplateType\BuilderiusTemplateTypesProviderInterface;
use Builderius\MooMoo\Platform\Bundle\TaxonomyBundle\Model\AbstractTaxonomy;
use Builderius\MooMoo\Platform\Bundle\TaxonomyBundle\Model\Term;

class BuilderiusTemplateTechnologyTaxonomy extends AbstractTaxonomy
{
    const NAME = 'builderius_template_technology';

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
        foreach ($this->templateTypesProvider->getTechnologies() as $technology) {
            $technologyName = $technology->getName();
            if (!array_key_exists($technologyName, $terms)) {
                $terms[$technologyName] = new Term(
                    [
                        Term::NAME_FIELD => $technologyName,
                        Term::SLUG_FIELD => $technologyName,
                        Term::TAXONOMY_FIELD => self::NAME
                    ]
                );
            }
        }

        return $terms;
    }
}
