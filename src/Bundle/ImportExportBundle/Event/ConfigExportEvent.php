<?php

namespace Builderius\Bundle\ImportExportBundle\Event;

use Builderius\Bundle\VCSBundle\Model\BuilderiusBranchInterface;
use Builderius\Bundle\VCSBundle\Model\BuilderiusCommitInterface;
use Builderius\Symfony\Contracts\EventDispatcher\Event;

class ConfigExportEvent extends Event
{
    const OWNER_TYPE = 'owner_type';

    /**
     * @var BuilderiusBranchInterface
     */
    private $branch;

    /**
     * @var BuilderiusCommitInterface
     */
    private $commit;

    /**
     * @var array
     */
    private $config;

    /**
     * @param array $config
     * @param BuilderiusBranchInterface $branch
     * @param BuilderiusCommitInterface|null $commit
     */
    public function __construct(
        array $config,
        BuilderiusBranchInterface $branch,
        BuilderiusCommitInterface $commit = null
    ) {
        $this->branch = $branch;
        $this->commit = $commit;
        $this->config = $config;
    }

    /**
     * @return BuilderiusBranchInterface
     */
    public function getBranch()
    {
        return $this->branch;
    }

    /**
     * @return BuilderiusCommitInterface|null
     */
    public function getCommit()
    {
        return $this->commit;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param array $config
     * @return $this
     */
    public function setConfig(array $config)
    {
        $this->config = $config;

        return $this;
    }
}