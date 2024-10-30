<?php

namespace Builderius\Bundle\TemplateBundle\ApplyRule\Checker;

use Builderius\Bundle\TemplateBundle\ApplyRule\Category\Registry\BuilderiusTemplateApplyRuleCategoriesRegistryInterface;
use Builderius\Bundle\TemplateBundle\ApplyRule\Converter\ApplyRuleConfigConverter;
use Builderius\Bundle\TemplateBundle\ApplyRule\Registry\BuilderiusTemplateApplyRulesRegistryInterface;
use Builderius\Bundle\TemplateBundle\Event\ApplyRuleSingleConfigEvent;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\EventDispatcher\EventDispatcher;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\Provider\PluginsVersionsProvider;
use Builderius\Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class BuilderiusTemplateApplyRulesChecker
{
    const CONJUNCTIONS = ['and', 'or'];
    const OPERATORS = ['==', '!='];

    /**
     * @var BuilderiusTemplateApplyRulesRegistryInterface
     */
    private $applyRulesRegistry;

    /**
     * @var BuilderiusTemplateApplyRuleCategoriesRegistryInterface
     */
    private $applyRuleCategoriesRegistry;

    /**
     * @var ExpressionLanguage
     */
    private $expressionLanguage;

    /**
     * @var PluginsVersionsProvider
     */
    private $pluginsVersionsProvider;

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @var array
     */
    private $applyRuleConfig;

    /**
     * @param BuilderiusTemplateApplyRulesRegistryInterface $applyRulesRegistry
     * @param BuilderiusTemplateApplyRuleCategoriesRegistryInterface $applyRuleCategoriesRegistry
     * @param ExpressionLanguage $expressionLanguage
     * @param PluginsVersionsProvider $pluginsVersionsProvider
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(
        BuilderiusTemplateApplyRulesRegistryInterface $applyRulesRegistry,
        BuilderiusTemplateApplyRuleCategoriesRegistryInterface $applyRuleCategoriesRegistry,
        ExpressionLanguage $expressionLanguage,
        PluginsVersionsProvider $pluginsVersionsProvider,
        EventDispatcher $eventDispatcher
    ) {
        $this->applyRulesRegistry = $applyRulesRegistry;
        $this->applyRuleCategoriesRegistry = $applyRuleCategoriesRegistry;
        $this->expressionLanguage = $expressionLanguage;
        $this->pluginsVersionsProvider = $pluginsVersionsProvider;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param array $applyRuleConfig
     * @return boolean
     * @throws \Exception
     */
    public function checkApplyRule(array $applyRuleConfig)
    {
        if (empty($applyRuleConfig)) {
            return false;
        }
        if (isset($applyRuleConfig['version'])) {
            $pluginsVersions = $this->getPluginsVersions();
            foreach ($applyRuleConfig['version'] as $name => $version) {
                if (!isset($pluginsVersions[$name]) || version_compare($pluginsVersions[$name], $version) === -1) {
                    return false;
                }
            }
        }
        $this->applyRuleConfig = $applyRuleConfig;
        $categoriesResults = [];
        if (is_array($applyRuleConfig['categories'])) {
            foreach ($applyRuleConfig['categories'] as $categoryName => $configSet) {
                foreach (ApplyRuleConfigConverter::convert($configSet) as $key => $config) {
                    if (in_array($key, self::CONJUNCTIONS)) {
                        $categoriesResults[] = $this->checkGroup($categoryName, $config, $key);
                    } else {
                        $categoriesResults[] = $this->checkSingle($categoryName, $config, $key);
                    }
                }
            }
        }

        return !in_array(false, $categoriesResults);
    }

    /**
     * @param string $categoryName
     * @param array $subConfigs
     * @param string $conjunction
     * @return bool
     */
    private function checkGroup($categoryName, array $subConfigs, $conjunction) {
        $subConfigsResults = [];
        foreach ($subConfigs as $subConfig) {
            foreach ($subConfig as $key => $subSubConfig) {
                if (in_array($key, self::CONJUNCTIONS)) {
                    $subConfigsResults[] = $this->checkGroup($categoryName, $subSubConfig, $key);
                } else {
                    $subConfigsResults[] = $this->checkSingle($categoryName, $subSubConfig, $key);
                }
            }
        }
        if ($conjunction === 'and') {
            return !in_array(false, $subConfigsResults);
        } else {
            return in_array(true, $subConfigsResults);
        }
    }

    /**
     * @param string $categoryName
     * @param array $config
     * @param string $operator
     * @return bool
     */
    private function checkSingle($categoryName, array $config, $operator)
    {
        $rules = explode('.', $config[0]['var']);
        $argument = $config[1];
        $expression = $this->generateExpression($categoryName, $rules, $argument);
        $category = $this->applyRuleCategoriesRegistry->getCategory($categoryName);
        $expression = str_replace('operator', $operator, $expression);
        $expressionResult = $this->expressionLanguage
            ->evaluate(
                $expression,
                [
                    $category->getVariableAlias() => $category->getVariableObject(),
                    'argument' => $argument
                ]
            );
        $event = new ApplyRuleSingleConfigEvent(
            $category,
            $expression,
            $expressionResult,
            $this->applyRuleConfig,
            $rules,
            $argument,
            $operator
        );
        $this->eventDispatcher->dispatch($event, 'builderius_template_single_config_apply_rule_checking');

        return $event->getExpressionResult();
    }

    /**
     * @param string $categoryName
     * @param array $rulesConfig
     * @param null $argument
     * @param string $expression
     * @param null $rootRule
     * @return string
     */
    private function generateExpression(
        $categoryName,
        array $rulesConfig,
        $argument = null,
        $expression = '',
        $rootRule = null
    ) {
        if ($rootRule === null) {
            $rootRule = $this->applyRulesRegistry->getRule($rulesConfig[0], $categoryName);
        }
        if ($rootRule) {
            if ($expression === '') {
                $expression = sprintf('%s', $rootRule->getExpression());
            } else {
                $subExpression = $rootRule->getExpression();
                if ($subExpression !== '') {
                    $expression = sprintf('%s and %s', $expression, $subExpression);
                }
            }
            array_shift($rulesConfig);
            if (!empty($rulesConfig)) {
                $childRule = $rootRule->getChild($rulesConfig[0]);
                if ($childRule) {
                    $expression = $this->generateExpression(
                        $categoryName,
                        $rulesConfig,
                        $argument,
                        $expression,
                        $childRule
                    );
                } else {
                    $variant = $rootRule->getVariant($rulesConfig[0]);
                    if ($variant) {
                        if ($expression === '') {
                            $expression = $variant->getExpression();
                        } else {
                            $expression = sprintf('%s and %s', $expression, $variant->getExpression());
                        }
                    }
                }
            }
        }

        return $expression;
    }

    /**
     * @return array
     */
    private function getPluginsVersions()
    {
        $versions = [];
        foreach ($this->pluginsVersionsProvider->getPluginsVersions() as $name => $version) {
            if (strpos($name, '.php') === false) {
                $versions[$name] = $version;
            }
        }

        return $versions;
    }
}
