<?php

namespace Builderius\Bundle\VCSBundle\Model;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag;

class BuilderiusBranch extends ParameterBag implements BuilderiusBranchInterface
{
    const ID_FIELD = 'id';
    const NAME_FIELD = 'name';
    const CREATED_AT_FIELD = 'created_at';
    const UPDATED_AT_FIELD = 'updated_at';
    const OWNER_ID_FIELD = 'owner_id';
    const OWNER_FIELD = 'owner';
    const BASE_BRANCH_NAME_FIELD = 'base_branch';
    const BASE_BRANCH_FIELD = 'base_branch_object';
    const BASE_COMMIT_NAME_FIELD = 'base_commit';
    const BASE_COMMIT_FIELD = 'base_commit_object';
    const ACTIVE_COMMIT_NAME_FIELD = 'active_commit';
    const ACTIVE_COMMIT_FIELD = 'active_commit_object';
    const PUBLISHED_COMMIT_NAME_FIELD = 'published_commit';
    const PUBLISHED_COMMIT_FIELD = 'published_commit_object';
    const COMMITS_FIELD = 'commits';
    const NOT_COMMITTED_CONFIG_FIELD = 'not_committed_config';
    const NCC_BASE_COMMIT_NAME_FIELD = 'ncc_base_commit_name';
    const AUTHOR_FIELD = 'author';
    const INNER_COMMITS_TAGS_FIELD = 'inner_commits_tags';
    const CONTENT_FIELD = 'content';

    const SERIALIZED_NOT_COMMITTED_CONFIG_GRAPHQL = 'serialized_not_committed_config';
    const BASE_BRANCH_NAME_GRAPHQL = 'base_branch_name';
    const BASE_BRANCH_GRAPHQL = 'base_branch';
    const BASE_COMMIT_NAME_GRAPHQL = 'base_commit_name';
    const BASE_COMMIT_GRAPHQL = 'base_commit';
    const ACTIVE_COMMIT_NAME_GRAPHQL = 'active_commit_name';
    const ACTIVE_COMMIT_GRAPHQL = 'active_commit';
    const PUBLISHED_COMMIT_NAME_GRAPHQL = 'published_commit_name';
    const PUBLISHED_COMMIT_GRAPHQL = 'published_commit';

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->get(self::ID_FIELD);
    }
    
    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->get(self::NAME_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        $this->set(self::NAME_FIELD, $name);
        
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        return $this->get(self::CREATED_AT_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->set(self::CREATED_AT_FIELD, $createdAt);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt()
    {
        return $this->get(self::UPDATED_AT_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt(\DateTime $updatedAt = null)
    {
        $this->set(self::UPDATED_AT_FIELD, $updatedAt);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOwnerId()
    {
        return $this->get(self::OWNER_ID_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setOwnerId($ownerId)
    {
        $this->set(self::OWNER_ID_FIELD, $ownerId);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOwner()
    {
        $field = $this->get(self::OWNER_FIELD);

        return $field instanceof \Closure ? $field() : $field;
    }

    /**
     * @inheritDoc
     */
    public function setOwner(BuilderiusVCSOwnerInterface $owner)
    {
        $this->set(self::OWNER_FIELD, $owner);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBaseBranchName()
    {
        return $this->get(self::BASE_BRANCH_NAME_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setBaseBranchName($branchName = null)
    {
        $this->set(self::BASE_BRANCH_NAME_FIELD, $branchName);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBaseBranch()
    {
        $field = $this->get(self::BASE_BRANCH_FIELD);

        return $field instanceof \Closure ? $field() : $field;
    }

    /**
     * @inheritDoc
     */
    public function setBaseBranch(BuilderiusBranchInterface $branch)
    {
        $this->set(self::BASE_BRANCH_FIELD, $branch);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBaseCommitName()
    {
        return $this->get(self::BASE_COMMIT_NAME_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setBaseCommitName($commitName = null)
    {
        $this->set(self::BASE_COMMIT_NAME_FIELD, $commitName);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBaseCommit()
    {
        $field = $this->get(self::BASE_COMMIT_FIELD);

        return $field instanceof \Closure ? $field() : $field;
    }

    /**
     * @inheritDoc
     */
    public function setBaseCommit(BuilderiusCommitInterface $commit)
    {
        $this->set(self::BASE_COMMIT_FIELD, $commit);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getActiveCommitName()
    {
        $field = $this->get(self::ACTIVE_COMMIT_NAME_FIELD);

        return $field instanceof \Closure ? $field() : $field;
    }

    /**
     * @inheritDoc
     */
    public function setActiveCommitName($commitName = null)
    {
        $this->set(self::ACTIVE_COMMIT_NAME_FIELD, $commitName);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getActiveCommit()
    {
        if ($activeCommitName = $this->getActiveCommitName()) {
            return $this->getCommit($activeCommitName);
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function setActiveCommit(BuilderiusCommitInterface $commit)
    {
        $this->set(self::ACTIVE_COMMIT_FIELD, $commit);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPublishedCommitName()
    {
        return $this->get(self::PUBLISHED_COMMIT_NAME_FIELD);
    }
    
    /**
     * @inheritDoc
     */
    public function setPublishedCommitName($commitName = null)
    {
        $this->set(self::PUBLISHED_COMMIT_NAME_FIELD, $commitName);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPublishedCommit()
    {
        if ($publishedCommitName = $this->getPublishedCommitName()) {
            return $this->getCommit($publishedCommitName);
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function setPublishedCommit(BuilderiusCommitInterface $commit)
    {
        $this->set(self::PUBLISHED_COMMIT_FIELD, $commit);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCommits()
    {
        $field = $this->get(self::COMMITS_FIELD);

        return $field instanceof \Closure ? $field() : $field;
    }

    /**
     * @inheritDoc
     */
    public function hasCommit($name)
    {
        $commits = $this->getCommits();
        
        return isset($commits[$name]);
    }
    
    /**
     * @inheritDoc
     */
    public function getCommit($name)
    {
        if ($this->hasCommit($name)) {
            return $this->getCommits()[$name];
        }
        
        return null;
    }

    /**
     * @inheritDoc
     */
    public function setCommits(array $commits)
    {
        /** @var BuilderiusCommitInterface $commit */
        foreach ($commits as $commit) {
            $commit->setBranch($this);
        }
        $this->set(self::COMMITS_FIELD, $commits);
        
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addCommit(BuilderiusCommitInterface $commit)
    {
        $commits = $this->getCommits();
        $commit->setBranch($this);
        $commits[$commit->getId()] = $commit;
        $this->set(self::COMMITS_FIELD, $commits);
        
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getNotCommittedConfig()
    {
        $field = $this->get(self::NOT_COMMITTED_CONFIG_FIELD);

        return $field instanceof \Closure ? $field() : $field;
    }

    /**
     * @inheritDoc
     */
    public function setNotCommittedConfig(array $contentConfig)
    {
        $this->set(self::NOT_COMMITTED_CONFIG_FIELD, $contentConfig);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getNccBaseCommitName()
    {
        $field = $this->get(self::NCC_BASE_COMMIT_NAME_FIELD);

        return $field instanceof \Closure ? $field() : $field;
    }

    /**
     * @inheritDoc
     */
    public function setNccBaseCommitName($name)
    {
        $this->set(self::NCC_BASE_COMMIT_NAME_FIELD, $name);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAuthor()
    {
        return $this->get(self::AUTHOR_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function getInnerCommitsTags()
    {
        $field = $this->get(self::INNER_COMMITS_TAGS_FIELD);

        return $field instanceof \Closure ? $field() : $field;
    }

    /**
     * @inheritDoc
     */
    public function setInnerCommitsTags(array $tags)
    {
        $this->set(self::INNER_COMMITS_TAGS_FIELD, $tags);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setAuthor(\WP_User $author)
    {
        $this->set(self::AUTHOR_FIELD, $author);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getContent($contentType = null)
    {
        $field = $this->get(self::CONTENT_FIELD);

        if ($field instanceof \Closure) {
            return $field($contentType);
        } else {
            $content = $this->get(self::CONTENT_FIELD);
            if ($contentType === null) {
                return $content;
            }
            if (is_array($content) && array_key_exists($contentType, $content)) {
                return $content[$contentType];
            }

            return null;
        }
    }

    /**
     * @inheritDoc
     */
    public function setContent(array $content = null)
    {
        $this->set(self::CONTENT_FIELD, $content);

        return $this;
    }
}
