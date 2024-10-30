<?php

namespace Builderius\Bundle\VCSBundle\Model;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag;

abstract class AbstractBuilderiusVCSOwner extends ParameterBag implements BuilderiusVCSOwnerInterface
{
    const ID_FIELD = 'id';
    const NAME_FIELD = 'name';
    const TITLE_FIELD = 'title';
    const ENTITY_TYPE_FIELD = 'entity_type';
    const TYPE_FIELD = 'type';
    const TECHNOLOGY_FIELD = 'technology';
    const CREATED_AT_FIELD = 'created_at';
    const UPDATED_AT_FIELD = 'updated_at';
    const ACTIVE_BRANCH_NAME_FIELD = 'active_branch';
    const ACTIVE_BRANCH_FIELD = 'active_branch_object';
    const DEFAULT_CONTENT_CONFIG_FIELD = 'default_content_config';
    const BRANCHES_FIELD = 'branches';
    const AUTHOR_FIELD = 'author';
    const INNER_COMMITS_TAGS_FIELD = 'inner_commits_tags';

    const ACTIVE_BRANCH_NAME_GRAPHQL = 'active_branch_name';
    const ACTIVE_BRANCH_GRAPHQL = 'active_branch';
    const BRANCH_GRAPHQL = 'branch';

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
    public function getTitle()
    {
        return $this->get(self::TITLE_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setTitle($title)
    {
        $this->set(self::TITLE_FIELD, $title);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getEntityType()
    {
        return $this->get(self::ENTITY_TYPE_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setEntityType($type)
    {
        $this->set(self::ENTITY_TYPE_FIELD, $type);

        return $this;
    }
    /**
     * @inheritDoc
     */
    public function getType()
    {
        return $this->get(self::TYPE_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setType($type)
    {
        $this->set(self::TYPE_FIELD, $type);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTechnology()
    {
        return $this->get(self::TECHNOLOGY_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setTechnology($technology)
    {
        $this->set(self::TECHNOLOGY_FIELD, $technology);

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
    public function getActiveBranchName()
    {
        $field = $this->get(self::ACTIVE_BRANCH_NAME_FIELD);

        return $field instanceof \Closure ? $field() : $field;
    }

    /**
     * @inheritDoc
     */
    public function setActiveBranchName($branchName)
    {
        $this->set(self::ACTIVE_BRANCH_NAME_FIELD, $branchName);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getActiveBranch()
    {
        $activeBranch = $this->get(self::ACTIVE_BRANCH_FIELD);
        if (!$activeBranch) {
            if ($activeBranchName = $this->getActiveBranchName()) {
                $activeBranch = $this->getBranch($activeBranchName);
                if ($activeBranch) {
                    $this->setActiveBranch($activeBranch);
                }
            }
        }

        return $activeBranch;
    }

    /**
     * @inheritDoc
     */
    public function setActiveBranch(BuilderiusBranchInterface $branch)
    {
        $this->set(self::ACTIVE_BRANCH_FIELD, $branch);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBranches()
    {
        $field = $this->get(self::BRANCHES_FIELD);

        return $field instanceof \Closure ? $field() : $field;
    }
    
    /**
     * @inheritDoc
     */
    public function getBranch($name)
    {
        if ($this->hasBranch($name)) {
            return $this->getBranches()[$name];
        }
        
        return null;
    }

    /**
     * @inheritDoc
     */
    public function hasBranch($name)
    {
        $branches = $this->getBranches();
        return isset($branches[$name]);
    }
    
    /**
     * @inheritDoc
     */
    public function setBranches(array $branches)
    {
        $branchesByNames = [];
        /** @var BuilderiusBranchInterface $branch */
        foreach ($branches as $branch) {
            $branch->setOwner($this);
            $branchesByNames[$branch->getName()] = $branch;
        }
        $this->set(self::BRANCHES_FIELD, $branchesByNames);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addBranch(BuilderiusBranchInterface $branch)
    {
        $branches = $this->getBranches();
        $branch->setOwner($this);
        $branches[$branch->getId()] = $branch;
        $this->set(self::BRANCHES_FIELD, $branches);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDefaultContentConfig()
    {
        $field = $this->get(self::DEFAULT_CONTENT_CONFIG_FIELD);

        return $field instanceof \Closure ? $field() : $field;
    }

    /**
     * @inheritDoc
     */
    public function setDefaultContentConfig($contentConfig)
    {
        $this->set(self::DEFAULT_CONTENT_CONFIG_FIELD, $contentConfig);

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
    public function setAuthor(\WP_User $author)
    {
        $this->set(self::AUTHOR_FIELD, $author);

        return $this;
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
}
