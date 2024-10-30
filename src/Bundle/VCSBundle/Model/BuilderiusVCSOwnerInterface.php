<?php

namespace Builderius\Bundle\VCSBundle\Model;

interface BuilderiusVCSOwnerInterface
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
     * @param string name
     * @return $this
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string title
     * @return $this
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getEntityType();

    /**
     * @param string $type
     * @return $this
     */
    public function setEntityType($type);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     * @return $this
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getTechnology();

    /**
     * @param string $technology
     * @return $this
     */
    public function setTechnology($technology);

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
     * @return \DateTime|null
     */
    public function getUpdatedAt();

    /**
     * @param \DateTime $updatedAt
     * @return $this
     */
    public function setUpdatedAt(\DateTime $updatedAt = null);

    /**
     * @return BuilderiusBranchInterface[]
     */
    public function getBranches();

    /**
     * @param string $name
     * @return BuilderiusBranchInterface|null
     */
    public function getBranch($name);

    /**
     * @return BuilderiusBranchInterface|null
     */
    public function getActiveBranch();

    /**
     * @param BuilderiusBranchInterface $branch
     * @return $this
     */
    public function setActiveBranch(BuilderiusBranchInterface $branch);

    /**
     * @param string $name
     * @return bool
     */
    public function hasBranch($name);

    /**
     * @param BuilderiusBranchInterface[] $branches
     * @return $this
     */
    public function setBranches(array $branches);

    /**
     * @param BuilderiusBranchInterface $branch
     * @return $this
     */
    public function addBranch(BuilderiusBranchInterface $branch);

    /**
     * @return string
     */
    public function getActiveBranchName();

    /**
     * @param string $branchName
     * @return $this
     */
    public function setActiveBranchName($branchName);

    /**
     * @return array
     */
    public function getDefaultContentConfig();

    /**
     * @param array $contentConfig
     * @return $this
     */
    public function setDefaultContentConfig(array $contentConfig);

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
    public function getInnerCommitsTags();

    /**
     * @return string[] $tags
     */
    public function setInnerCommitsTags(array $tags);

}