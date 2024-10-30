<?php

namespace Builderius\Bundle\ResponsiveBundle\Strategy;

class MobileFirstBuilderiusResponsiveStrategy extends AbstractBuilderiusResponsiveStrategy
{
    const NAME = 'mobile-first';

    /**
     * @inheritDoc
     */
    public function sort(array $mediaQueries)
    {
        usort($mediaQueries, function($a, $b) {
            $testIsPrint = $this->testIsPrint($a, $b);
            if ($testIsPrint !== 0) {
                return $testIsPrint;
            }
            $minA = $this->isMinWidth($a) || $this->isMinHeight($a);
            $maxA = $this->isMaxWidth($a) || $this->isMaxHeight($a);

            $minB = $this->isMinWidth($b) || $this->isMinHeight($b);
            $maxB = $this->isMaxWidth($b) || $this->isMaxHeight($b);

            if ($minA && $maxB) {
                return -1;
            }
            if ($maxA && $minB) {
                return 1;
            }

            $lengthA = $this->getQueryLength($a);
            $lengthB = $this->getQueryLength($b);

            if ($lengthA === PHP_INT_MAX && $lengthB === PHP_INT_MAX) {
                return strcmp($a, $b);
            } else if ($lengthA === PHP_INT_MAX) {
                return 1;
            } else if ($lengthB === PHP_INT_MAX) {
                return -1;
            }

            if ($lengthA > $lengthB) {
                if ($maxA) {
                    return -1;
                }
                return 1;
            }

            if ($lengthA < $lengthB) {
                if ($maxA) {
                    return 1;
                }
                return -1;
            }

            return strcmp($a, $b);
        });

        return $mediaQueries;
    }
}