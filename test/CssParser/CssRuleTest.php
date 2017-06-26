<?php

namespace Wallace;

use Wallace\Models\CssParser\CssRule;
use Wallace\Models\CssParser\CssSelector;
use PHPUnit\Framework\TestCase;

class CssRuleTest extends TestCase
{
    /**
     * @dataProvider getSelectorsProvider
     */
    public function testGetSelectors($rule, $selectors)
    {
        $this->assertEquals($rule->getSelectors(), $selectors);
        $this->assertInternalType('array', $rule->getSelectors());
        $this->assertContainsOnly('string', $rule->getSelectors());
    }

    public function getSelectorsProvider()
    {
        return [
            [
                new CssRule('.a { x:y }'),
                [
                  '.a'
                ]
            ],
            [
                new CssRule('.a, #foo bar .baz ["derp=foo"] { x:y }'),
                [
                  '.a',
                  '#foo bar .baz ["derp=foo"]'
                ]
            ]
        ];
    }

    /**
     * @dataProvider isEmptySuccessProvider
     */
    public function testIsEmptySuccess($rule)
    {
        $this->assertTrue($rule->isEmpty());
    }

    public function isEmptySuccessProvider()
    {
        return [
            [new CssRule('.a {}')],
            [new CssRule('.b{  }')],
        ];
    }

    /**
     * @dataProvider isEmptyFailureProvider
     */
    public function testIsEmptyFailure($rule)
    {
        $this->assertFalse($rule->isEmpty());
    }

    public function isEmptyFailureProvider()
    {
        return [
            [new CssRule('.a { x:y }')],
            [new CssRule('.b{x:y}')],
        ];
    }

    /**
     * @dataProvider getDeclarationsProvider
     */
    public function testGetDeclarations($rule, $declarations)
    {
        $this->assertEquals($rule->getDeclarations(), $declarations);
        $this->assertInternalType('array', $rule->getDeclarations());
    }

    public function getDeclarationsProvider()
    {
        return [
            [
                new CssRule('.a { color: red; background: green; }'),
                [
                    'color: red',
                    'background: green'
                ]
            ]
        ];
    }
}
