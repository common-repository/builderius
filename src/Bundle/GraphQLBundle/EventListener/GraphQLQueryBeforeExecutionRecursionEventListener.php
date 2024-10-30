<?php

namespace Builderius\Bundle\GraphQLBundle\EventListener;

use Builderius\Bundle\GraphQLBundle\Event\GraphQLQueryBeforeExecutionEvent;
use Builderius\Bundle\GraphQLBundle\Event\GraphQLQueryExecutedEvent;
use Builderius\GraphQL\Language\AST\ArgumentNode;
use Builderius\GraphQL\Language\AST\NodeList;
use Builderius\GraphQL\Language\AST\SelectionSetNode;
use Builderius\GraphQL\Language\Parser;
use Builderius\GraphQL\Language\Source;

class GraphQLQueryBeforeExecutionRecursionEventListener
{
    /**
     * @var string
     */
    private $offset;

    /**
     * @var int
     */
    private $index;

    /**
     * @var int
     */
    private $recursionDepth = 10;

    /**
     * @var int
     */
    private $recursionCount = 0;

    /**
     * @param GraphQLQueryExecutedEvent $event
     */
    public function beforeQueryExecution(GraphQLQueryBeforeExecutionEvent $event)
    {
        $this->recursionCount = 0;
        $query = $event->getQuery();
        if (is_string($query) && strpos($query, '@recursive') !== false) {
            $documentNode = Parser::parse(new Source($query ?? '', 'GraphQL'));
            $tst = $documentNode->toArray(true);
            $rootSelectionSet = $this->processSelectionSetArr($tst['definitions'][0]['selectionSet']);
            $tst['definitions'][0]['selectionSet'] = $rootSelectionSet;
            $q = $this->generateGraphQLQuery($tst['definitions'][0]);
            $event->setQuery($q);
        }
    }

    /**
     * @param array $selectionSet
     * @return array
     */
    private function processSelectionSetArr(array $selectionSet)
    {
        if (!empty($selectionSet['selections'])) {
            $sell = $selectionSet['selections'];
            $newSell = [];
            foreach ($sell as $i => $selection) {
                $isRecursive = $this->isRecursiveArr($selection['directives']);
                if (isset($selection['selectionSet']) && null != $selection['selectionSet']) {
                    if ($isRecursive) {
                        $set = $selection['selectionSet'];
                        $sel = $set['selections'];
                        $foundSameName = false;
                        if ($this->recursionCount + 1 <= $this->recursionDepth) {
                            if (null === $this->offset) {
                                foreach ($sel as $k => $sv) {
                                    if ($sv['name']['value'] == $selection['name']['value']) {
                                        unset($sel[$k]);
                                        $sel[$k] =  $selection;
                                        $foundSameName = true;
                                    }
                                }
                                if ($foundSameName === false) {
                                    if (null === $this->index) {
                                        $sel[count($sel)] = $selection;
                                    } else {
                                        $found = false;
                                        $newSel = [];
                                        foreach($sel as $ii => $v) {
                                            if ($ii === $this->index) {
                                                $newSel[$ii] = $selection;
                                                $found = true;
                                            }
                                            $newSel[] = $v;
                                        }
                                        if (false === $found) {
                                            $newSel[] = $selection;
                                        }
                                        $sel = $newSel;
                                    }
                                }
                            } else {
                                foreach ($sel as $k => $sv) {
                                    if ($sv['name']['value'] == $this->offset && null != $sv['selectionSet']) {
                                        $set2 = $sv['selectionSet'];
                                        $sel2 = $set2['selections'];
                                        foreach ($sel2 as $k2 => $sv2) {
                                            if ($sv2['name']['value'] == $selection['name']['value']) {
                                                unset($sel2[$k2]);
                                                $sel2[$k2] =  $selection;
                                                $foundSameName = true;
                                            }
                                        }
                                        if ($foundSameName === false) {
                                            if (null === $this->index) {
                                                $sel2[count($sel2)] = $selection;
                                            } else {
                                                $found = false;
                                                $newSel = [];
                                                foreach($sel2 as $ii => $v) {
                                                    if ($ii === $this->index) {
                                                        $newSel[$ii] = $selection;
                                                        $found = true;
                                                    }
                                                    $newSel[] = $v;
                                                }
                                                if (false === $found) {
                                                    $newSel[] = $selection;
                                                }
                                                $sel2 = $newSel;
                                            }
                                        }
                                        $sel[$k]['selectionSet']['selections'] = $sel2;
                                    }
                                }
                            }
                        } else {
                            if (null === $this->offset) {
                                foreach ($sel as $k => $sv) {
                                    if ($sv['name']['value'] == $selection['name']['value']) {
                                        unset($sel[$k]);
                                    }
                                }
                            } else {
                                foreach ($sel as $k => $sv) {
                                    if ($sv['name']['value'] == $this->offset && null != $sv['selectionSet']) {
                                        $set2 = $sv['selectionSet'];
                                        $sel2 = $set2['selections'];
                                        foreach ($sel2 as $k2 => $sv2) {
                                            if ($sv2['name']['value'] == $selection['name']['value']) {
                                                unset($sel2[$k2]);
                                            }
                                        }
                                        $sel[$k]['selectionSet']['selections'] = $sel2;
                                    }
                                }
                            }
                        }
                        $set['selections'] = $sel;
                        $selection['selectionSet'] = $set;
                    }
                    $sett = $this->processSelectionSetArr($selection['selectionSet']);
                    $selection['selectionSet'] = $sett;
                }
                $newSell[$i] = $selection;
            }
            $selectionSet['selections'] = $newSell;
        }

        return $selectionSet;
    }

    /**
     * @param array $directives
     * @return bool
     */
    private function isRecursiveArr(array $directives)
    {
        $isRecursive = false;
        if ($this->recursionCount >= $this->recursionDepth) {
            return $isRecursive;
        }
        foreach ($directives as $directive) {
            if ($directive['name']['value'] === 'recursive') {
                $isRecursive = true;
                $args = $directive['arguments'];
                foreach ($args as $arg) {
                    if ($arg['name']['value'] === 'depth') {
                        $val = intval($arg['value']['value']);
                        if ($val > 1 && $val < 20) {
                            $this->recursionDepth = $val;
                        }
                    }
                    if ($arg['name']['value'] === 'index') {
                        $this->index = intval($arg['value']['value']);
                    }
                    if ($arg['name']['value'] === 'offset') {
                        $this->offset = $arg['value']['value'];
                    }
                }
                $this->recursionCount++;
                break;
            }
        }

        return $isRecursive;
    }

    /**
     * @param SelectionSetNode $selectionSet
     * @return SelectionSetNode
     */
    private function processSelectionSet(SelectionSetNode $selectionSet)
    {
        if ($selectionSet->selections) {
            $sell = $selectionSet->selections;
            $newSell = [];
            foreach ($sell as $i => $selection) {
                $isRecursive = $this->isRecursive($selection->directives);
                if ($selection->selectionSet) {
                    if ($isRecursive) {
                        $set = $selection->selectionSet;
                        /** @var NodeList $sel */
                        $sel = $set->selections;
                        $foundSameName = false;
                        if ($this->recursionCount + 1 <= $this->recursionDepth) {
                            if (null === $this->offset) {
                                foreach ($sel as $k => $sv) {
                                    if ($sv->name->value == $selection->name->value) {
                                        $sel->offsetUnset($k);
                                        $sel->offsetSet($k, clone $selection);
                                        $foundSameName = true;
                                    }
                                }
                                if ($foundSameName === false) {
                                    $sel->offsetSet($sel->count(), clone $selection);
                                }
                            } else {
                                foreach ($sel as $k => $sv) {
                                    if ($sv->name->value == $this->offset && null != $sv->selectionSet) {
                                        $set2 = $sv->selectionSet;
                                        /** @var NodeList $sel2 */
                                        $sel2 = $set2->selections;
                                        $foundSameName = false;
                                    }
                                }
                            }
                        } else {
                            if (null === $this->offset) {
                                foreach ($sel as $k => $sv) {
                                    if ($sv->name->value == $selection->name->value) {
                                        $sel->offsetUnset($k);
                                    }
                                }
                            }
                        }
                        $set->selections = clone $sel;
                        $selection->selectionSet = clone $set;
                    }
                    $sett = $this->processSelectionSet($selection->selectionSet);
                    $selection->selectionSet = clone $sett;
                }
                $newSell[$i] = clone $selection;
            }
            $selectionSet->selections = new NodeList($newSell);
        }
        return $selectionSet;
    }

    /**
     * @param $directives
     * @return bool
     */
    private function isRecursive($directives)
    {
        $isRecursive = false;
        if ($this->recursionCount >= $this->recursionDepth) {
            return $isRecursive;
        }
        foreach ($directives as $directive) {
            if ($directive->name->value === 'recursive') {
                $isRecursive = true;
                $args = $directive->arguments;
                /** @var ArgumentNode $arg */
                foreach ($args as $arg) {
                    if ($arg->name->value === 'depth') {
                        $val = intval($arg->value->value);
                        if ($val > 1 && $val < 20) {
                            $this->recursionDepth = $val;
                        }
                    }
                    if ($arg->name->value === 'offset') {
                        $this->offset = $arg->value->value;
                    }
                }
                $this->recursionCount++;
                break;
            }
        }

        return $isRecursive;
    }

    public function generateGraphQLQuery($data) {
        $result = '';
        foreach ($data as $key => $value) {
                switch ($key) {
                    case 'operation':
                        $result .= $value . ' ';
                        break;
                    case 'kind':
                        if ($value === 'NullValue') {
                            $result .= 'null';
                        }
                        break;
                    case 'loc':
                        break;
                    case 'block':
                        break;
                    case 'alias':
                        break;
                    case 'variableDefinitions':
                        break;
                    case 'name':
                        if (isset($data['alias'])) {
                            $result .= $data['alias']['value'] . ': ';
                        }
                        $result .= (is_array($value) ? $value['value'] : $value) . ' ';
                        break;
                    case 'value':
                        if (is_array($value) && isset($value['kind']) && $value['kind'] === 'StringValue') {
                            $result .= '"' . $value['value'] . '"';
                        } elseif (is_array($value) && isset($value['kind']) && $value['kind'] !== 'StringValue') {
                            $result .= $value['value'];
                        } elseif (isset($data['kind']) && $data['kind'] === 'BooleanValue') {
                            $result .= $value === true ? 'true' : 'false';
                        } elseif (isset($data['kind']) && $data['kind'] !== 'StringValue') {
                            $result .= $value;
                        } else {
                            $result .= '"' . str_replace('"', '\\"', $value) . '"';
                        }
                        break;
                    case 'arguments':
                        if (!empty($value)) {
                            $result .= '(';
                            foreach ($value as $argument) {
                                $result .= $argument['name']['value'] . ': ' . $this->generateGraphQLQuery($argument['value']) . ', ';
                            }
                            $result = rtrim($result, ', ');
                            $result .= ') ';
                        }
                        break;
                    case 'fields':
                        $result .= "{\n ";
                        foreach ($value as $field) {
                            $result .= $field['name']['value'] . ': ' . $this->generateGraphQLQuery($field['value']) . ', ';
                        }
                        $result = rtrim($result, ', ');
                        $result .= "}\n ";
                        break;
                    case 'values':
                        $result .= '[ ';
                        foreach ($value as $val) {
                            $result .= $this->generateGraphQLQuery($val) . ', ';
                        }
                        $result = rtrim($result, ', ');
                        $result .= '] ';
                        break;
                    case 'selections':
                        $result .= "{\n ";
                        foreach ($value as $selection) {
                            $result .= $this->generateGraphQLQuery($selection);
                        }
                        $result .= "}\n ";
                        break;
                    case 'directives':
                        foreach ($value as $directive) {
                            $result .= ' @' . $directive['name']['value'];
                        }
                        $result .= ' ';
                        break;
                    case 'selectionSet':
                        $result .= $this->generateGraphQLQuery($value);
                        break;
                    default:
                        $result .= $key . ' ';
                        break;
                }
        }

        return $result;
    }
}