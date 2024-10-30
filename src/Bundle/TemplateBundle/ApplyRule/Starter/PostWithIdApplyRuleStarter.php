<?php

namespace Builderius\Bundle\TemplateBundle\ApplyRule\Starter;

use Builderius\Bundle\TemplateBundle\ApplyRule\Provider\AvailablePostsProvider;

class PostWithIdApplyRuleStarter implements BuilderiusTemplateApplyRuleStarterInterface
{
    /**
     * @var AvailablePostsProvider
     */
    private $availablePostsProvider;

    /**
     * @param AvailablePostsProvider $availablePostsProvider
     */
    public function __construct(AvailablePostsProvider $availablePostsProvider)
    {
        $this->availablePostsProvider = $availablePostsProvider;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'blog_post_with_id';
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        $availablePosts = $this->availablePostsProvider->getArguments();

        return sprintf("Blog post with ID#%d", $availablePosts[0]['value']);
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
        $availablePosts = $this->availablePostsProvider->getArguments();

        return [
            "theme" => [
                "condition" => "and",
                "rules" => [
                    [
                        "value" => "singular",
                        "type" => "chain",
                        "name" => "singular",
                        "rules" => [
                            [
                                "value" => "single",
                                "type" => "chain",
                                "name" => "single",
                                "rules" => [
                                    [
                                        "value" => "blog_post",
                                        "type" => "chain",
                                        "name" => "blog_post",
                                        "rules" => [
                                            [
                                                "type" => "select",
                                                "name" => "with_id",
                                                "value" => $availablePosts[0]['value'],
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
                    ],
                ],
                "type" => "group",
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function isValid()
    {
        return !empty($this->availablePostsProvider->getArguments());
    }
}