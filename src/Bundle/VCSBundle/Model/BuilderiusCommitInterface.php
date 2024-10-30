<?php

namespace Builderius\Bundle\VCSBundle\Model;

interface BuilderiusCommitInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description);

    /**
     * @return BuilderiusBranchInterface
     */
    public function getBranchId();

    /**
     * @param int $branchId
     * @return $this
     */
    public function setBranchId($branchId);

    /**
     * @return BuilderiusBranchInterface
     */
    public function getBranch();

    /**
     * @param BuilderiusBranchInterface $branch
     * @return $this
     */
    public function setBranch(BuilderiusBranchInterface $branch);

    /**
     * @return string|null
     */
    public function getMergedBranchName();

    /**
     * @param string|null $branchName
     * @return $this
     */
    public function setMergedBranchName($branchName = null);

    /**
     * @return BuilderiusBranchInterface|null
     */
    public function getMergedBranch();

    /**
     * @param BuilderiusBranchInterface $branch
     * @return $this
     */
    public function setMergedBranch(BuilderiusBranchInterface $branch);

    /**
     * @return string|null
     */
    public function getMergedCommitName();

    /**
     * @param string|null $commitName
     * @return $this
     */
    public function setMergedCommitName($commitName = null);

    /**
     * @return BuilderiusCommitInterface|null
     */
    public function getMergedCommit();

    /**
     * @param BuilderiusCommitInterface $commit
     * @return $this
     */
    public function setMergedCommit(BuilderiusCommitInterface $commit);

    /**
     * @return \DateTime
     */
    public function getCreatedAt();
    
    /**
     * @param \DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt);
    
    /**
     * @return array
     */
    public function getContentConfig();

    /**
     * @param array $contentConfig
     * @return $this
     */
    public function setContentConfig(array $contentConfig);

    /**
     * @return \WP_User
     */
    public function getAuthor();

    /**
     * @param \WP_User $author
     * @return $this
     */
    public function setAuthor(\WP_User $author);

    /**
     * @return string[]
     */
    public function getTags();

    /**
     * @return string[] $tags
     */
    public function setTags(array $tags);

    /**
     * @return mixed
     */
    public function getContent($contentType = null);

    /**
     * @param array|null $content
     * @return string
     */
    public function setContent(array $content = null);
}
