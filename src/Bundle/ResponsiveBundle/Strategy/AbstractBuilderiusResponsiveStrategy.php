<?php

namespace Builderius\Bundle\ResponsiveBundle\Strategy;

abstract class AbstractBuilderiusResponsiveStrategy implements BuilderiusResponsiveStrategyInterface
{
    const NAME = null;

    const minMaxWidth = '/(!?\(\s*min(-device-)?-width)(.|\n)+\(\s*max(-device)?-width/i';
    const minWidth = '/\(\s*min(-device)?-width/i';
    const maxMinWidth = '/(!?\(\s*max(-device)?-width)(.|\n)+\(\s*min(-device)?-width/i';
    const maxWidth = '/\(\s*max(-device)?-width/i';

    const minMaxHeight = '/(!?\(\s*min(-device)?-height)(.|\n)+\(\s*max(-device)?-height/i';
    const minHeight = '/\(\s*min(-device)?-height/i';
    const maxMinHeight = '/(!?\(\s*max(-device)?-height)(.|\n)+\(\s*min(-device)?-height/i';
    const maxHeight = '/\(\s*max(-device)?-height/i';

    const isPrint = '/print/i';
    const isPrintOnly = '/^print$/i';

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return static::NAME;
    }

    /**
     * @param string $query
     * @param string $doubleTestTrue
     * @param string $doubleTestFalse
     * @param string $singleTest
     * @return bool
     */
    protected function testQuery($query, $doubleTestTrue, $doubleTestFalse, $singleTest) {
        $matches = [];
        if (!empty(preg_match($doubleTestTrue, $query, $matches))) {
            return true;
        } else if (!empty(preg_match($doubleTestFalse, $query, $matches))) {
            return false;
        }
        preg_match($singleTest, $query, $matches);
        
        return !empty($matches);
    }

    /**
     * @param string $query
     * @return bool
     */
    protected function isMinWidth($query)
    {
        return $this->testQuery(
            $query,
            self::minMaxWidth,
            self::maxMinWidth,
            self::minWidth
        );
    }

    /**
     * @param string $query
     * @return bool
     */
    protected function isMaxWidth($query)
    {
        return $this->testQuery(
            $query,
            self::maxMinWidth,
            self::minMaxWidth,
            self::maxWidth
        );
    }

    /**
     * @param string $query
     * @return bool
     */
    protected function isMinHeight($query)
    {
        return $this->testQuery(
            $query,
            self::minMaxHeight,
            self::maxMinHeight,
            self::minHeight
        );
    }

    /**
     * @param string $query
     * @return bool
     */
    protected function isMaxHeight($query)
    {
        return $this->testQuery(
            $query,
            self::maxMinHeight,
            self::minMaxHeight,
            self::maxHeight
        );
    }

    /**
     * @param string $a
     * @param string $b
     * @return int
     */
    protected function testIsPrint($a, $b) {
        $isPrintAMatches = [];
        preg_match(self::isPrint, $a, $isPrintAMatches);
        $isPrintA = !empty($isPrintAMatches);
        $isPrintOnlyAMatches = [];
        preg_match(self::isPrintOnly, $a, $isPrintOnlyAMatches);
        $isPrintOnlyA = !empty($isPrintOnlyAMatches);

        $isPrintBMatches = [];
        preg_match(self::isPrint, $b, $isPrintBMatches);
        $isPrintB = !empty($isPrintBMatches);
        $isPrintOnlyBMatches = [];
        preg_match(self::isPrintOnly, $b, $isPrintOnlyBMatches);
        $isPrintOnlyB = !empty($isPrintOnlyBMatches);

        if ($isPrintA && $isPrintB) {
            if (!$isPrintOnlyA && $isPrintOnlyB) {
                return 1;
            }
            if ($isPrintOnlyA && !$isPrintOnlyB) {
                return -1;
            }

            return strcmp($a, $b);
        }
        if ($isPrintA) {
            return 1;
        }
        if ($isPrintB) {
            return -1;
        }

        return 0;
    }

    protected function getQueryLength($query) {
        $length = [];
        preg_match('/(-?\d*\.?\d+)(ch|em|ex|px|rem)/', $query, $length);

        if (empty($length)) {
            return PHP_INT_MAX;
        }

        $number = floatval($length[1]);
        $unit = $length[2];

        switch ($unit) {
            case 'ch':
                $number = $number * 8.8984375;
                break;

            case 'em':
            case 'rem':
                $number = $number * 16;
                break;

            case 'ex':
                $number = $number * 8.296875;
                break;

            case 'px':
                break;
        }

        return $number;
    }
}