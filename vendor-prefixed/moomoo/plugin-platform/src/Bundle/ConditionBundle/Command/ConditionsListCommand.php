<?php

namespace Builderius\MooMoo\Platform\Bundle\ConditionBundle\Command;

use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Registry\ConditionsRegistryInterface;
use Builderius\MooMoo\Platform\Bundle\WpCliBundle\Model\WpCliCommandInterface;
class ConditionsListCommand implements \Builderius\MooMoo\Platform\Bundle\WpCliBundle\Model\WpCliCommandInterface
{
    const NAME = 'moomoo:conditions-list';
    /**
     * @var ConditionsRegistryInterface
     */
    private $conditionsRegistry;
    public function __construct(\Builderius\MooMoo\Platform\Bundle\ConditionBundle\Registry\ConditionsRegistryInterface $conditionsRegistry)
    {
        $this->conditionsRegistry = $conditionsRegistry;
    }
    /**
     * @inheritDoc
     */
    public function getName()
    {
        return self::NAME;
    }
    /**
     * @inheritDoc
     */
    public function execute($arguments = [])
    {
        $conditions = $this->conditionsRegistry->getConditions();
        $maxName = \max(\array_map('strlen', \array_keys($conditions)));
        \WP_CLI::line(\sprintf('%sDescription', \str_pad('Name', $maxName + 10)));
        \ksort($conditions);
        foreach ($conditions as $condition) {
            \WP_CLI::line(\sprintf('%s%s', \str_pad($condition->getName(), $maxName + 10), $condition->getDescription()));
        }
    }
    /**
     * @inheritDoc
     */
    public function getAdditionalRegistrationParameters()
    {
        return ['shortdesc' => 'Shows list of all available conditions'];
    }
}
