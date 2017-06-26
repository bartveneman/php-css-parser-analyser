<?php

namespace Wallace;

use Wallace\Models\CssParser\CssSelector;
use Wallace\Models\CssParser\CssSpecificity;
use PHPUnit\Framework\TestCase;

class CssSelectorTest extends TestCase
{
    /**
     * @dataProvider getSelectorProvider
     */
    public function testGetSelector($selectorObj, $expected)
    {
        $this->assertEquals($selectorObj->getSelector(), $expected);
        $this->assertInternalType('string', $selectorObj->getSelector());
    }

    public function getSelectorProvider()
    {
        return [
            [
                new CssSelector('.selector'),
                '.selector'
            ],
            [
                new CssSelector('.such #selector ~ many i[class="identifiers"]'),
                '.such #selector ~ many i[class="identifiers"]'
            ]
        ];
    }

    /**
     * @dataProvider getIdentifiersProvider
     */
    public function testGetIdentifiers($selector, $expected, $count)
    {
        $this->assertEquals($selector->getIdentifiers(), $expected);
        $this->assertContainsOnly('string', $selector->getIdentifiers());
        $this->assertInternalType('array', $selector->getIdentifiers());
        $this->assertCount($count, $selector->getIdentifiers());
    }

    public function getIdentifiersProvider()
    {
        return [
            [
                new CssSelector('.such #selector ~ many i[class="identifiers"]'),
                [
                    '.such',
                    '#selector',
                    'many',
                    'i',
                    '[class="identifiers"]',
                ],
                5
            ],
            [
                new CssSelector('.coolblue-content .box_service p.openinghours strong.open_tomorrow'),
                [
                    '.coolblue-content',
                    '.box_service',
                    'p',
                    '.openinghours',
                    'strong',
                    '.open_tomorrow',
                ],
                6
            ],
            [
                new CssSelector('.slds-checkbox--toggle [type=checkbox][disabled]:checked+.slds-checkbox--faux_container .slds-checkbox--faux:before'),
                [
                    '.slds-checkbox--toggle',
                    '[type=checkbox]',
                    '[disabled]',
                    ':checked',
                    '.slds-checkbox--faux_container',
                    '.slds-checkbox--faux',
                    ':before',
                ],
                7
            ]
        ];
    }

    /**
     * @dataProvider getIsBrowserHackSuccessProvider
     */
    public function testIsBrowserHackSuccess($selector)
    {
        $this->assertTrue((new CssSelector($selector))->isBrowserHack());
    }

    public function getIsBrowserHackSuccessProvider()
    {
        return [
            ['* html .selector'],
            ['*:first-child + html .selector'],
            ['*:first-child+html .selector'],
            ['* + html .selector'],
            ['*+html .selector'],
            ['body*.selector'],
            ['html > body .selector'],
            ['html>body .selector'],
            ['.selector\\'],
            [':root .selector'],
            ['body:last-child .selector'],
            ['body:nth-of-type(1) .selector'],
            ['body:first-of-type .selector'],
            ['.selector:not([attr*=\'\'])'],
            ['.selector:not([attr*=""])'],
            ['.selector:not(*:root)'],
            ['body:empty .selector'],
            ['x:-moz-any-link'],
            ['body:not(:-moz-handler-blocked) .selector'],
            ['_::-moz-progress-bar'],
            ['_::-moz-range-track'],
            ['_:-moz-tree-row(hover)'],
            ['_::selection'],
            ['x:-IE7'],
            ['_:-ms-fullscreen'],
            ['_:-ms-input-placeholder'],
            ['html:first-child .selector'],
            ['_:-o-prefocus'],
            ['*|html[xmlns*=""] .selector'],
            ['html[xmlns*=""] body:last-child .selector'],
            ['html[xmlns*=""]:root .selector '],
            ['_::-moz-svg-foreign-content'],
        ];
    }

    /**
     * @dataProvider getIsBrowserHackFailureProvider
     */
    public function testIsBrowserHackFailure($selector)
    {
        $this->assertFalse((new CssSelector($selector))->isBrowserHack());
    }

    public function getIsBrowserHackFailureProvider()
    {
        return [
            ['tbody:first-child'],
            ['.slds-card__body:empty'],
            ['html *'],
        ];
    }

    public function testSpecificity()
    {
        $selector = new CssSelector('.my-selector');
        $this->assertInternalType('array', $selector->getSpecificity());
    }
}
