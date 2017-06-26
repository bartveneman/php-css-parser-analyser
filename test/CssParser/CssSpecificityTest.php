<?php

namespace Wallace;

use Wallace\Models\CssParser\CssSpecificity;
use PHPUnit\Framework\TestCase;

class CssSpecificityTest extends TestCase
{
    public function testSpecificityStructure()
    {
        $actual = new CssSpecificity('.selector');

        $this->assertArrayHasKey('a', $actual->getSpecificity());
        $this->assertArrayHasKey('b', $actual->getSpecificity());
        $this->assertArrayHasKey('c', $actual->getSpecificity());
    }

    /**
     * @dataProvider getSpecificityProvider
     */
    public function testGetSpecificity($specificity, $expected)
    {
        $this->assertEquals($expected, $specificity->getSpecificity());
    }

    public function getSpecificityProvider()
    {
        return [
            [
                new CssSpecificity('.simple article'),
                [
                  'a' => 0,
                  'b' => 1,
                  'c' => 1
                ]
            ],
            [
                new CssSpecificity('.complex #article::after ~ element[with^="foo"]:after + article'),
                [
                  'a' => 1,
                  'b' => 2,
                  'c' => 4
                ]
            ],
            [
                new CssSpecificity('.cw-footer .cw_main_footer #cw-trust_icons .cw-trust_icons-item.cw-trustpilot .cw-trustpilot-count img'),
                [
                  'a' => 1,
                  'b' => 5,
                  'c' => 1
                ]
            ]
        ];
    }
}
