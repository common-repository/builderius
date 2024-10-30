<?php

namespace Builderius\Bundle\VCSBundle\Model;

interface BuilderiusBranchInterface
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
     * @return int
     */
    public function getOwnerId();

    /**
     * @param int $ownerId
     * @return $this
     */
    public function setOwnerId($ownerId);

    /**
     * @return BuilderiusVCSOwnerInterface
     */
    public function getOwner();
    
    /**
     * @param BuilderiusVCSOwnerInterface $owner
     * @return $this
     */
    public function setOwner(BuilderiusVCSOwnerInterface $owner);

    /**
     * @return string|null
     */
    public function getBaseBranchName();

    /**
     * @param string|null $branchName
     * @return $this
     */
    public function setBaseBranchName($branchName = null);

    /**
     * @return BuilderiusBranchInterface|null
     */
    public function getBaseBranch();

    /**
     * @param BuilderiusBranchInterface $branch
     * @return $this
     */
    public function setBaseBranch(BuilderiusBranchInterface $branch);

    /**
     * @return string|null
     */
    public function getBaseCommitName();

    /**
     * @param string|null $commitName
     * @return $this
     */
    public function setBaseCommitName($commitName = null);

    /**
     * @return BuilderiusCommitInterface|null
     */
    public function getBaseCommit();

    /**
     * @param BuilderiusCommitInterface $commit
     * @return $this
     */
    public function setBaseCommit(BuilderiusCommitInterface $commit);

    /**
     * @return string|null
     */
    public function getActiveCommitName();
    
    /**
     * @param string|null $commitName
     * @return $this
     */
    public function setActiveCommitName($commitName = null);

    /**
     * @return BuilderiusCommitInterface|null
     */
    public function getActiveCommit();

    /**
     * @param BuilderiusCommitInterface $commit
     */
    public function setActiveCommit(BuilderiusCommitInterface $commit);

    /**
     * @return string|null
     */
    public function getPublishedCommitName();

    /**
     * @param BuilderiusCommitInterface $commit
     */
    public function setPublishedCommit(BuilderiusCommitInterface $commit);

    /**
     * @param string|null $commitName
     * @return $this
     */
    public function setPublishedCommitName($commitName = null);

    /**
     * @return BuilderiusCommitInterface|null
     */
    public function getPublishedCommit();

    /**
     * @return BuilderiusCommitInterface[]
     */
    public function getCommits();

    /**
     * @param string $name
     * @return bool
     */
    public function hasCommit($name);

    /**
     * @param string $name
     * @return BuilderiusCommitInterface|null
     */
    public function getCommit($name);

    /**
     * @param BuilderiusCommitInterface[] $commits
     * @return $this
     */
    public function setCommits(array $commits);
    
    /**
     * @param BuilderiusCommitInterface $commit
     * @return $this
     */
    public function addCommit(BuilderiusCommitInterface $commit);
    
    /**
     * @return array
     */
    public function getNotCommittedConfig();
    
    /**
     * @param array $contentConfig
     * @return $this
     */
    public function setNotCommittedConfig(array $contentConfig);

    /**
     * @return string
     */
    public function getNccBaseCommitName();

    /**
     * @param string $name
     * @return $this
     */
    public function setNccBaseCommitName($name);

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
