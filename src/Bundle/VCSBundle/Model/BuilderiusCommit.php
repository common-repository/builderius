<?php

namespace Builderius\Bundle\VCSBundle\Model;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag;

class BuilderiusCommit extends ParameterBag implements BuilderiusCommitInterface
{
    const ID_FIELD = 'id';
    const NAME_FIELD = 'name';
    const DESCRIPTION_FIELD = 'description';
    const BRANCH_ID_FIELD = 'branch_id';
    const BRANCH_FIELD = 'branch';
    const MERGED_BRANCH_NAME_FIELD = 'merged_branch_name';
    const MERGED_BRANCH_FIELD = 'merged_branch';
    const MERGED_COMMIT_NAME_FIELD = 'merged_commit_name';
    const MERGED_COMMIT_FIELD = 'merged_commit';
    const CREATED_AT_FIELD = 'created_at';
    const CONTENT_CONFIG_FIELD = 'content_config';
    const SERIALIZED_CONTENT_CONFIG_GRAPHQL = 'serialized_content_config';
    const AUTHOR_FIELD = 'author';
    const TAGS_FIELD = 'tags';
    const CONTENT_FIELD = 'content';

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
    public function getDescription()
    {
        return $this->get(self::DESCRIPTION_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setDescription($description)
    {
        $this->set(self::DESCRIPTION_FIELD, $description);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBranchId()
    {
        return $this->get(self::BRANCH_ID_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setBranchId($branchId)
    {
        $this->set(self::BRANCH_ID_FIELD, $branchId);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBranch()
    {
        $field = $this->get(self::BRANCH_FIELD);

        return $field instanceof \Closure ? $field() : $field;
    }

    /**
     * @inheritDoc
     */
    public function setBranch(BuilderiusBranchInterface $branch)
    {
        $this->set(self::BRANCH_FIELD, $branch);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMergedBranchName()
    {
        return $this->get(self::MERGED_BRANCH_NAME_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setMergedBranchName($branchName = null)
    {
        $this->set(self::MERGED_BRANCH_NAME_FIELD, $branchName);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMergedBranch()
    {
        $field = $this->get(self::MERGED_BRANCH_FIELD);

        return $field instanceof \Closure ? $field() : $field;
    }

    /**
     * @inheritDoc
     */
    public function setMergedBranch(BuilderiusBranchInterface $branch)
    {
        $this->set(self::MERGED_BRANCH_FIELD, $branch);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMergedCommitName()
    {
        return $this->get(self::MERGED_COMMIT_NAME_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setMergedCommitName($commitName = null)
    {
        $this->set(self::MERGED_COMMIT_NAME_FIELD, $commitName);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMergedCommit()
    {
        $field = $this->get(self::MERGED_COMMIT_FIELD);

        return $field instanceof \Closure ? $field() : $field;
    }

    /**
     * @inheritDoc
     */
    public function setMergedCommit(BuilderiusCommitInterface $commit)
    {
        $this->set(self::MERGED_COMMIT_FIELD, $commit);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTags()
    {
        return $this->get(self::TAGS_FIELD, []);
    }

    /**
     * @inheritDoc
     */
    public function setTags(array $tags)
    {
        $this->set(self::TAGS_FIELD, $tags);

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
    public function getContentConfig()
    {
        return $this->get(self::CONTENT_CONFIG_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setContentConfig(array $contentConfig)
    {
        $this->set(self::CONTENT_CONFIG_FIELD, $contentConfig);

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
    public function getContent($contentType = null)
    {
        $content = $this->get(self::CONTENT_FIELD);
        if ($contentType === null) {
            return $content;
        }
        if (is_array($content) && array_key_exists($contentType, $content)) {
            return $content[$contentType];
        }

        return null;
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
