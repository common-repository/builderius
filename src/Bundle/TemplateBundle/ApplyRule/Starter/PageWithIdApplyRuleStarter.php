<?php

namespace Builderius\Bundle\TemplateBundle\ApplyRule\Starter;

use Builderius\Bundle\TemplateBundle\ApplyRule\Provider\AvailablePagesProvider;

class PageWithIdApplyRuleStarter implements BuilderiusTemplateApplyRuleStarterInterface
{
    /**
     * @var AvailablePagesProvider
     */
    private $availablePagesProvider;

    /**
     * @param AvailablePagesProvider $availablePagesProvider
     */
    public function __construct(AvailablePagesProvider $availablePagesProvider)
    {
        $this->availablePagesProvider = $availablePagesProvider;
    }


    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'page_with_id';
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        $availablePages = $this->availablePagesProvider->getArguments();

        return sprintf("Page with ID#%d", $availablePages[0]['value']);
    }

    /**
     * @inheritDoc
     */
    public function getCategory()
    {
        return 'theme';
    }

    /**
     * @inheritDoc
     */
    public function getTemplateTypes()
    {
        return ['template'];
    }

    /**
     * @inheritDoc
     */
    public function getTechnologies()
    {
        return ['html'];
    }

    /**
     * @inheritDoc
     */
    public function getConfig()
    {
        $availablePages = $this->availablePagesProvider->getArguments();

        return [
            "theme" => [
                "rules" => [
                    [
                        "value" => "singular",
                        "type" => "chain",
                        "name" => "singular",
                        "rules" => [
                            [
                                "value" => "page",
                                "type" => "chain",
                                "name" => "page",
                                "rules" => [
                                    [
                                        "type" => "select",
                                        "name" => "with_id",
                                        "value" => $availablePages[0]['value'],
                                        "operator" => "==",
                                    ],
                                ],
                                "condition" => "and",
                            ],
                        ],
                        "condition" => "and",
                    ],
                ],
                "condition" => "and",
                "type" => "group",
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function isValid()
    {
        return !empty($this->availablePagesProvider->getArguments());
    }
}