<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\ExpressionLanguage;

use Builderius\Bundle\TestingBundle\Tests\Functional\AbstractContainerAwareTestCase;
use Builderius\Symfony\Component\ExpressionLanguage\ExpressionLanguage;

abstract class AbstractElFunctionTest extends AbstractContainerAwareTestCase
{
    /**
     * @var ExpressionLanguage
     */
    protected $el;

    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->el =
            $this->container->get('builderius_el.expression_language');
    }

    /**
     * @dataProvider dataProvider
     * @param string $expression
     * @param mixed $expectedResult
     */
    public function testFunction($expression, $expectedResult, array $context = [])
    {
        static::assertEquals($expectedResult, $this->el->evaluate($expression, $context));
    }
}