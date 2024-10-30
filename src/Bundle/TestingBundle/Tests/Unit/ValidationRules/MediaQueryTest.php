<?php

namespace Builderius\Bundle\TestingBundle\Tests\Unit\ValidationRules;

use Builderius\Bundle\SettingBundle\Validation\Rule\MediaQuery;
use PHPUnit\Framework\TestCase;

class MediaQueryTest extends TestCase
{
    /**
     * @var MediaQuery
     */
    private $rule;

    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        $this->rule = new MediaQuery();
    }
    /**
     * @dataProvider dataProvider
     * @param string $input
     * @param bool $expectedResult
     */
    public function testValidate($input, $expectedResult)
    {
        $actualResult = $this->rule->validate($input);
        self::assertEquals($expectedResult, $actualResult);
    }

    /**
     * @return array
     */
    public function dataProvider()
    {
        return [
            [
                'input' => '@media @media screen and (min-width: 320px) and (max-width: 767px)',
                'expectedResult' => false
            ],
            [
                'input' => '@media screen and (min-width: 320.55px), (max-width: 767.55px)',
                'expectedResult' => true
            ],
            [
                'input' => '@media screen and (min-height: 480px)',
                'expectedResult' => true
            ],
            [
                'input' => '@media screen and (min-height: 480px) , ',
                'expectedResult' => false
            ],
            [
                'input' => '@media screen and (min-height: 480px) and ',
                'expectedResult' => false
            ],
            [
                'input' => '@media screen and (min-height: 480)',
                'expectedResult' => false
            ],
            [
                'input' => '@media screen and (min-height 480px)',
                'expectedResult' => false
            ],
            [
                'input' => '@media screen and (min-height: 480px), (orientation: portrait)',
                'expectedResult' => true
            ],
            [
                'input' => '@media not screen and (min-height: 480px)',
                'expectedResult' => true
            ],
            [
                'input' => '@media only screen and (min-height: 480px)',
                'expectedResult' => true
            ],
            [
                'input' => '@media print',
                'expectedResult' => true
            ],
            [
                'input' => '@media',
                'expectedResult' => false
            ],
            [
                'input' => '@media ',
                'expectedResult' => false
            ],
            [
                'input' => '@media @media ',
                'expectedResult' => false
            ],
            [
                'input' => '@media only screen and (resolution: 100dpi)',
                'expectedResult' => true
            ],
            [
                'input' => '@media only screen , , (resolution: 100dpi)',
                'expectedResult' => false
            ],
            [
                'input' => '@media only screen and and (resolution: 100dpi)',
                'expectedResult' => false
            ],
            [
                'input' => '@media only screen and (resolution: 100px)',
                'expectedResult' => false
            ],
            [
                'input' => '@media screen and (aspect-ratio: 10/16)',
                'expectedResult' => true
            ],
            [
                'input' => '@media screen and (aspect-ratio: 10)',
                'expectedResult' => false
            ]
        ];
    }
}