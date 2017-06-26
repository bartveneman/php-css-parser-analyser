<?php

namespace Wallace;

use Wallace\Models\CssParser\CssSupportsRule;
use PHPUnit\Framework\TestCase;

class CssSupportsRuleTest extends TestCase
{
    public function testConstructor()
    {
        $instance = new CssSupportsRule('@supports (-foo: green) {}');
        $this->assertInstanceOf(
            'Wallace\Models\CssParser\CssSupportsRule',
            $instance
        );
    }

    /**
     * @dataProvider getConditionSuccessProvider
     */
    public function testGetConditionSuccess($rule, $expected)
    {
        $supportsRule = new CssSupportsRule($rule);
        $this->assertEquals($expected, $supportsRule->getCondition());
    }

    /**
     * @dataProvider getConditionSuccessProvider
     */
    public function testIsBrowserHackFailure($rule)
    {
        $supportsRule = new CssSupportsRule($rule);
        $this->assertFalse($supportsRule->isBrowserHack());
    }

    public function getConditionSuccessProvider()
    {
        return [
            [
                '@supports (transform-origin: 5% 5%) {}',
                '(transform-origin: 5% 5%)'
            ],
            [
                '@supports not ( transform-origin: 10em 10em 10em ) {}',
                'not ( transform-origin: 10em 10em 10em )'
            ],
            [
                '@supports not ( not ( transform-origin: 2px ) ) {}',
                'not ( not ( transform-origin: 2px ) )'
            ],
            [
                '@supports (display: flexbox) and ( not (display: inline-grid) ) {}',
                '(display: flexbox) and ( not (display: inline-grid) )'
            ],
            [
                '@supports (display: table-cell) and (display: list-item) {}',
                '(display: table-cell) and (display: list-item)'
            ],
            [
                '@supports (display: table-cell) and (display: list-item) and (display:run-in) {}',
                '(display: table-cell) and (display: list-item) and (display:run-in)'
            ],
            [
                '@supports (display: table-cell) and ((display: list-item) and (display:run-in)) {}',
                '(display: table-cell) and ((display: list-item) and (display:run-in))'
            ],
            [
                '@supports ( transform-style: preserve ) or ( -moz-transform-style: preserve ) {}',
                '( transform-style: preserve ) or ( -moz-transform-style: preserve )'
            ],
            [
                '@supports ( transform-style: preserve ) or ( -moz-transform-style: preserve ) or ( -o-transform-style: preserve ) or ( -webkit-transform-style: preserve  ) {}',
                '( transform-style: preserve ) or ( -moz-transform-style: preserve ) or ( -o-transform-style: preserve ) or ( -webkit-transform-style: preserve  )'
            ],
            [
                '@supports (animation-name: test) {}',
                '(animation-name: test)'
            ],
            [
                '@supports ( (perspective: 10px) or (-moz-perspective: 10px) or (-webkit-perspective: 10px) or (-ms-perspective: 10px) or (-o-perspective: 10px) ) {}',
                '( (perspective: 10px) or (-moz-perspective: 10px) or (-webkit-perspective: 10px) or (-ms-perspective: 10px) or (-o-perspective: 10px) )'
            ],
            [
                '@supports not ((text-align-last:justify) or (-moz-text-align-last:justify) ) {}',
                'not ((text-align-last:justify) or (-moz-text-align-last:justify) )'
            ],
            [
                '@supports (--foo: green) {}',
                '(--foo: green)'
            ],
        ];
    }

    /**
     * @dataProvider getIsBrowserHackSuccessProvider
     */
    public function testIsBrowserHackSuccess($rule)
    {
        $supportsRule = new CssSupportsRule($rule);
        $this->assertTrue($supportsRule->isBrowserHack());
    }

    public function getIsBrowserHackSuccessProvider()
    {
        return [
            ['@supports (-webkit-appearance:none) {}'],
            ['@supports (-moz-appearance:meterbar) {}'],
            ['@supports (-moz-appearance:meterbar) and (display:flex) {}'],
            ['@supports (-moz-appearance:meterbar) and (cursor:zoom-in) {}'],
            ['@supports (-moz-appearance:meterbar) and (background-attachment:local) {}'],
            ['@supports (-moz-appearance:meterbar) and (image-orientation:90deg) {}'],
            ['@supports (-moz-appearance:meterbar) and (all:initial) {}'],
            ['@supports (-moz-appearance:meterbar) and (list-style-type:japanese-formal) {}'],
            ['@supports (-moz-appearance:meterbar) and (background-blend-mode:difference,normal) {}'],
        ];
    }

    /**
     * @dataProvider getRulesInheritenceProvider
     */
    public function testGetRulesInheritence($rule, $expected)
    {
        $supportsRule = new CssSupportsRule($rule);
        $this->assertEquals($expected, $supportsRule->getRules());
    }

    public function getRulesInheritenceProvider()
    {
        return [
            [
                '@supports (filter: blur(5px)) { .foo { filter:blur(5px) } }',
                [
                    '.foo { filter:blur(5px) }'
                ]
            ],
        ];
    }
}
