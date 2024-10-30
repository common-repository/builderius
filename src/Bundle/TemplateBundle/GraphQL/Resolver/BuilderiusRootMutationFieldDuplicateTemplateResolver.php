<?php

namespace Builderius\Bundle\TemplateBundle\GraphQL\Resolver;

use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLFieldResolverInterface;
use Builderius\Bundle\TemplateBundle\Event\PostContainingEvent;
use Builderius\Bundle\TemplateBundle\Factory\BuilderiusTemplateFromPostFactory;
use Builderius\Bundle\TemplateBundle\Model\BuilderiusTemplate;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplatePostType;
use Builderius\Bundle\VCSBundle\GraphQL\Resolver\BuilderiusRootMutationFieldCreateCommitResolver;
use Builderius\Bundle\VCSBundle\Model\BuilderiusCommit;
use Builderius\GraphQL\Type\Definition\ResolveInfo;
use Builderius\Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BuilderiusRootMutationFieldDuplicateTemplateResolver  implements GraphQLFieldResolverInterface
{
    /**
     * @var BuilderiusTemplateFromPostFactory
     */
    private $templateFactory;

    /**
     * @var BuilderiusRootMutationFieldCreateTemplateResolver
     */
    private $templateCreateResolver;

    /**
     * @var BuilderiusRootMutationFieldCreateCommitResolver
     */
    private $commitCreateResolver;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var \WP_Query
     */
    protected $wpQuery;

    /**
     * @param BuilderiusTemplateFromPostFactory $templateFactory
     * @param BuilderiusRootMutationFieldCreateTemplateResolver $templateCreateResolver
     * @param BuilderiusRootMutationFieldCreateCommitResolver $commitCreateResolver
     * @param EventDispatcherInterface $eventDispatcher
     * @param \WP_Query $wpQuery
     */
    public function __construct(
        BuilderiusTemplateFromPostFactory $templateFactory,
        BuilderiusRootMutationFieldCreateTemplateResolver $templateCreateResolver,
        BuilderiusRootMutationFieldCreateCommitResolver $commitCreateResolver,
        EventDispatcherInterface $eventDispatcher,
        \WP_Query $wpQuery
    ) {
        $this->templateFactory = $templateFactory;
        $this->templateCreateResolver = $templateCreateResolver;
        $this->commitCreateResolver = $commitCreateResolver;
        $this->eventDispatcher = $eventDispatcher;
        $this->wpQuery = $wpQuery;
    }

    /**
     * @inheritDoc
     */
    public function getTypeNames()
    {
        return ['BuilderiusRootMutation'];
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
    public function isApplicable($objectValue, array $args, $context, ResolveInfo $info)
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getFieldName()
    {
        return 'duplicateTemplate';
    }

    /**
     * @inheritDoc
     */
    public function resolve($objectValue, array $args, $context, ResolveInfo $info)
    {
        $postToBeDuplicated = get_post((int)$args['id']);
        if (empty($postToBeDuplicated) || empty($postToBeDuplicated->ID) ||
            BuilderiusTemplatePostType::POST_TYPE !== $postToBeDuplicated->post_type) {
            throw new \Exception('Invalid Template ID.', 400);
        }
        $this->eventDispatcher->dispatch(new PostContainingEvent($postToBeDuplicated), 'builderius_template_before_duplicate');

        $templateToBeDuplicated = $this->templateFactory->createTemplate($postToBeDuplicated);
        $input = [
            BuilderiusTemplate::TITLE_FIELD => $this->getDuplicatedTitle($templateToBeDuplicated->getTitle()),
            BuilderiusTemplate::TYPE_FIELD => $templateToBeDuplicated->getType(),
            BuilderiusTemplate::SUB_TYPE_FIELD => $templateToBeDuplicated->getSubType(),
            BuilderiusTemplate::TECHNOLOGY_FIELD => $templateToBeDuplicated->getTechnology(),
            BuilderiusTemplate::SORT_ORDER_FIELD => $templateToBeDuplicated->getSortOrder(),
            BuilderiusTemplate::SERIALIZED_APPLY_RULES_CONFIG_GRAPHQL => json_encode($templateToBeDuplicated->getApplyRulesConfig(), JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE),
        ];
        if ($templateToBeDuplicated->getSubType() === 'hook') {
            $input[BuilderiusTemplate::HOOK_FIELD] = $templateToBeDuplicated->getHook();
            $input[BuilderiusTemplate::HOOK_TYPE_FIELD] = $templateToBeDuplicated->getHookType();
            $input[BuilderiusTemplate::HOOK_ACCEPTED_ARGS_FIELD] = $templateToBeDuplicated->getHookAcceptedArgs();
            $input[BuilderiusTemplate::CLEAR_EXISTING_HOOKS_FIELD] = $templateToBeDuplicated->isClearExistingHooks();
        }
        $duplicateTemplateResult = $this->templateCreateResolver->resolve($objectValue, ['input' => $input], $context, $info);
        if ($duplicateTemplateResult instanceof \ArrayObject && isset($duplicateTemplateResult['template'])) {
            $duplicatedTemplate = $duplicateTemplateResult['template'];
            $branch = $templateToBeDuplicated->getActiveBranch();
            if ($branch) {
                $commit = $branch->getActiveCommit();
                $newBranch = $duplicatedTemplate->getActiveBranch();
                if ($commit && $newBranch) {
                    $commitInput = [
                        BuilderiusCommit::BRANCH_ID_FIELD => $newBranch->getId(),
                        BuilderiusCommit::SERIALIZED_CONTENT_CONFIG_GRAPHQL => json_encode($this->getDuplicatedConfig($commit->getContentConfig()), JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE)
                    ];
                    $this->commitCreateResolver->resolve($objectValue, ['input' => $commitInput], $context, $info);
                }
            }
            $duplicatedPost = get_post($duplicatedTemplate->getId());
            $duplicatedTemplate = $this->templateFactory->createTemplate($duplicatedPost);
            $this->eventDispatcher->dispatch(new PostContainingEvent($duplicatedPost), 'builderius_template_duplicated');

            return new \ArrayObject(['template' => $duplicatedTemplate]);
        }

        return new \ArrayObject(['template' => []]);
    }

    /**
     * @param string $originalTitle
     * @return string
     */
    private function getDuplicatedTitle($originalTitle)
    {
        $postsWithSameTitle = $this->wpQuery->query([
            'post_type' => BuilderiusTemplatePostType::POST_TYPE,
            'title' => $originalTitle,
            'post_status' => get_post_stati(),
            'posts_per_page' => 1,
            'no_found_rows' => true,
        ]);
        if (!empty($postsWithSameTitle)) {
            $tier1 = explode(' (Copy', $originalTitle);
            $realTitle = $tier1[0];
            if (!isset($tier1[1])) {
                $originalTitle = $realTitle . ' (Copy)';
            } elseif ($tier1[1] === ')') {
                $originalTitle = $realTitle . ' (Copy2)';
            } else {
                $tier2 = explode(')', $tier1[1]);
                $originalTitle = $realTitle . ' (Copy' . ((int)$tier2[0] + 1) . ')';
            }
            return $this->getDuplicatedTitle($originalTitle);
        } else {
            return $originalTitle;
        }
    }

    /**
     * @param array $config
     * @return array
     */
    private function getDuplicatedConfig(array $config)
    {
        $duplicatedConfig = $config;
        $duplicatedConfig['indexes'] = [];
        $duplicatedConfig['modules'] = [];

        $tmp = [];
        foreach ($config['modules'] as $id => $module) {
            $newId = $this->generateIndex();
            $tmp[$id] = $newId;
            $duplicatedConfig['modules'][$newId] = $module;
        }
        foreach ($duplicatedConfig['modules'] as &$module) {
            $module['id'] = $tmp[$module['id']];
            $module['parent'] = isset($tmp[$module['parent']]) ? $tmp[$module['parent']] : '';
        }
        if (isset($config['indexes']['root'])) {
            $duplicatedConfig['indexes']['root'] = [];
            foreach ($config['indexes']['root'] as $k => $idx) {
                $duplicatedConfig['indexes']['root'][$k] = $tmp[$idx];
            }
        }
        foreach ($config['indexes'] as $i => $children) {
            if ($i !== 'root') {
                $duplicatedConfig['indexes'][$tmp[$i]] = [];
                foreach ($children as $n => $key) {
                    $duplicatedConfig['indexes'][$tmp[$i]][$n] = $tmp[$key];
                }
            }
        }

        return $duplicatedConfig;
    }

    private function generateIndex()
    {
        return 'u' . substr(bin2hex(random_bytes(9)), 0, 9);
    }
}