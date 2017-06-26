<?php

namespace Wallace;

use Wallace\Models\CssParser\CssKeyframe;
use PHPUnit\Framework\TestCase;

class CssKeyframesTest extends TestCase
{
    /**
     * @dataProvider getIdentifierProvider
     */
    public function testGetIdentifier($keyframe, $expected)
    {
        $this->assertEquals($keyframe->getIdentifier(), $expected);
        $this->assertInternalType('string', $keyframe->getIdentifier());
    }

    public function getIdentifierProvider()
    {
        return [
            [
                new CssKeyframe('@keyframes identifier {}'),
                'identifier'
            ],
            [
                new CssKeyframe('@-webkit-keyframes webkitKeyframe0123 {}'),
                'webkitKeyframe0123'
            ],
            [
                new CssKeyframe('@-moz-keyframes mozKeyframe-12 {}'),
                'mozKeyframe-12'
            ],
            [
                new CssKeyframe('@-o-keyframes oKeyframe-_09 {}'),
                'oKeyframe-_09'
            ],
        ];
    }

    /**
     * @dataProvider getRulesProvider
     */
    public function testGetRules($keyframe, $expectedRules)
    {
        $this->assertEquals($expectedRules, $keyframe->getRules());
        $this->assertContainsOnly('string', $keyframe->getRules());
    }

    public function getRulesProvider()
    {
        return [
            [
                new CssKeyframe('@keyframes test { 0% { z-index: 1; } }'),
                [
                  '0% { z-index: 1; }',
                ],
            ],
            [
                new CssKeyframe('@keyframes test { 0% { z-index: 1; } to { z-index: 2 } }'),
                [
                  '0% { z-index: 1; }',
                  'to { z-index: 2 }',
                ],
            ],
        ];
    }

    /**
     * @dataProvider isPrefixedTrueProvider
     */
    public function testIsPrefixed($keyframe)
    {
        $this->assertTrue($keyframe->isPrefixed());
    }

    public function isPrefixedTrueProvider()
    {
        return [
            [new CssKeyframe('@-webkit-keyframes HELLO {}')],
            [new CssKeyframe('@-moz-keyframes HELLO {}')],
            [new CssKeyframe('@-o-keyframes HELLO {}')],
        ];
    }

    /**
     * @dataProvider isPrefixedFalseProvider
     */
    public function testIsPrefixedFailure($keyframe)
    {
        $this->assertFalse($keyframe->isPrefixed());
    }

    public function isPrefixedFalseProvider()
    {
        return [
            [new CssKeyframe('@keyframes HELLO {}')],
        ];
    }

    /**
     * @dataProvider getRulesInheritenceProvider
     */
    public function testGetRulesInheritence($rule, $expected)
    {
        $keyframe = new CssKeyframe($rule);
        $this->assertEquals($expected, $keyframe->getRules());
    }

    public function getRulesInheritenceProvider()
    {
        return [
            [
                '@keyframes dance { 0% { color: green } 100% { color: red } }',
                [
                  '0% { color: green }',
                  '100% { color: red }',
                ]
            ],
        ];
    }
}
