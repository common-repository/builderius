<?php

namespace Builderius\Bundle\ResponsiveBundle\Strategy;

interface BuilderiusResponsiveStrategyInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param array $mediaQueries
     * @return array
     */
    public function sort(array $mediaQueries);
}