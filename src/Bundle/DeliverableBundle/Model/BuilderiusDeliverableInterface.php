<?php

namespace Builderius\Bundle\DeliverableBundle\Model;

interface BuilderiusDeliverableInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getTag();

    /**
     * @param string $tag
     * @return $this
     */
    public function setTag($tag);

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
     * @param string|null $entityType
     * @param string|null $technology
     * @param string|null $type
     * @return BuilderiusDeliverableSubModuleInterface[]
     */
    public function getSubModules($entityType = null, $technology = null, $type = null);

    /**
     * @param BuilderiusDeliverableSubModuleInterface[] $subModules
     * @return $this
     */
    public function setSubModules(array $subModules);

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
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @param \DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt);

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus($status);
}