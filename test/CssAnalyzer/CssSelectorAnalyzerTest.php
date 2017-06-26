<?php

namespace Wallace;

use Wallace\Models\CssAnalyzer\CssAnalyzer;
use Wallace\Models\CssAnalyzer\CssSelectorAnalyzer;
use PHPUnit\Framework\TestCase;

class CssSelectorAnalyzerTest extends TestCase
{

    protected function setUp()
    {
        $css = file_get_contents('./test/fixtures/analyzer/selectors.css');
        $analyzer = new CssAnalyzer($css, './temp');
        $this->analyzer = new CssSelectorAnalyzer($analyzer->selectors);
    }

    protected function tearDown()
    {
        unset($this->analyzer);
    }

    public function testTotalSelectors()
    {
        $expected = 51;
        $actual = $this->analyzer->getTotalSelectors();

        $this->assertEquals($expected, $actual);
    }

    public function testTotalUniqueSelectors()
    {
        $expected = 50;
        $actual = $this->analyzer->getTotalUniqueSelectors();

        $this->assertEquals($expected, $actual);
    }

    public function testIdSelectors()
    {
        $expected = [
            ".Foo > .Bar ~ .Baz [type=\"text\"] + span:before #bazz #fizz #buzz #drank #drugs",
            "#jsSelector",
            "#foo",
            "#multipe #ids #counted #as #one"
        ];
        $actual = $this->analyzer->getIdSelectors();

        $this->assertEquals($expected, $actual);
        $this->assertContainsOnly('string', $actual);
        $this->assertEquals(4, $this->analyzer->getTotalIdSelectors());
    }

    public function testUniversalSelectors()
    {
        $expected = [
            "*",
            ".foo *",
            ".foo * .bar",
            "* html .selector",
            "*:first-child + html .selector",
            "*:first-child+html .selector",
            "* + html .selector",
            "*+html .selector",
            "body*.selector",
            ".selector:not(*:root)",
            "*|html[xmlns*=\"\"] .selector",
        ];
        $actual = $this->analyzer->getUniversalSelectors();

        $this->assertEquals($expected, $actual);
        $this->assertContainsOnly('string', $actual);
    }

    public function testTotalUniversalSelectors()
    {
        $this->assertEquals(12, $this->analyzer->getTotalUniversalSelectors());
    }

    public function testJsSelectors()
    {
        $expected = [
            '.js-toggle-item',
            '.JSFOO',
            '.adjecent.js-selector',
            '#jsSelector',
            '[class*="js-selector"]',
            '[id=\'js-selector\']'
        ];
        $actual = $this->analyzer->getJsSelectors();

        $this->assertEquals($expected, $actual);
        $this->assertContainsOnly('string', $actual);
        $this->assertEquals(6, $this->analyzer->getTotalJsSelectors());
    }

    public function testMaxSpecificitySelectors()
    {
        $expected = ['.Foo > .Bar ~ .Baz [type="text"] + span:before #bazz #fizz #buzz #drank #drugs'];
        $actual = $this->analyzer->getMaxSpecificitySelectors();

        $this->assertEquals($expected, $actual);
    }

    public function testMaxSpecificity()
    {
        $expected = [
            'a' => 5,
            'b' => 4,
            'c' => 2
        ];
        $actual = $this->analyzer->getMaxSpecificity();

        $this->assertEquals($expected, $actual);
    }

    public function testMostIdentifiersSelectors()
    {
        $expected = ['.a .b .c .d .e .f .g .h .i .j .k .l .m .n .o .p .q .r .s .t .u .v .w .x .y .z'];
        $actual = $this->analyzer->getMaxIdentifierSelectors();

        $this->assertEquals($expected, $actual);
    }

    public function testIdentifiersCount()
    {
        $expected = 26;
        $actual = $this->analyzer->getMaxIdentifiers();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @depends testTotalSelectors
     */
    public function testAvgIdentifiersPerSelector()
    {
        $actual = $this->analyzer->getAverageIdentifiers();

        $this->assertGreaterThanOrEqual(2.8, $actual);
        $this->assertLessThanOrEqual(3.0, $actual);
        $this->assertInternalType('float', $actual);
    }

    public function testBrowserHacks()
    {
        $expected = [
            '* html .selector',
            '*:first-child + html .selector',
            '*:first-child+html .selector',
            '* + html .selector',
            '*+html .selector',
            'body*.selector',
            'html > body .selector',
            'html>body .selector',
            '.selector\\',
            ':root .selector',
            'body:last-child .selector',
            'body:nth-of-type(1) .selector',
            'body:first-of-type .selector',
            '.selector:not([attr*=\'\'])',
            '.selector:not([attr*=""])',
            '.selector:not(*:root)',
            'body:empty .selector',
            'x:-moz-any-link',
            'body:not(:-moz-handler-blocked) .selector',
            '_::-moz-progress-bar',
            '_::-moz-range-track',
            '_:-moz-tree-row(hover)',
            '_::selection',
            'x:-IE7',
            '_:-ms-fullscreen',
            '_:-ms-input-placeholder',
            'html:first-child .selector',
            '_:-o-prefocus',
            '*|html[xmlns*=""] .selector',
            'html[xmlns*=""] body:last-child .selector',
            'html[xmlns*=""]:root .selector',
            '_::-moz-svg-foreign-content',
        ];
        $actual = $this->analyzer->getBrowserHacks();

        $this->assertEquals($expected, $actual);
        $this->assertContainsOnly('string', $actual);
    }

    public function testTotalBrowserHacks()
    {
        $this->assertEquals(33, $this->analyzer->getTotalBrowserHacks());
    }
}
