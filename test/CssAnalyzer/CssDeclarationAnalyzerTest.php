<?php

namespace Wallace;

use Wallace\Models\CssAnalyzer\CssAnalyzer;
use Wallace\Models\CssAnalyzer\CssDeclarationAnalyzer;
use PHPUnit\Framework\TestCase;

class CssDeclarationAnalyzerTest extends TestCase
{

    protected function setUp()
    {
        $css = file_get_contents('./test/fixtures/analyzer/declarations.css');
        $analyzer = new CssAnalyzer($css, './temp');
        $this->analyzer = new CssDeclarationAnalyzer($analyzer->declarations);
    }

    protected function tearDown()
    {
        unset($this->analyzer);
    }

    public function testTotalDeclarations()
    {
        $expected = 74;
        $actual = $this->analyzer->getTotalDeclarations();

        $this->assertEquals($expected, $actual);
    }

    public function testTotalPrefixedDeclarations()
    {
        $expected = 4;
        $actual = $this->analyzer->getTotalPrefixedDeclarations();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @depends testTotalPrefixedDeclarations
     * @depends testTotalDeclarations
     */
    public function testPrefixedDeclarationsRatio()
    {
        $expected = 4 / 74;
        $actual = $this->analyzer->getPrefixedDeclarationsShare();

        $this->assertEquals($expected, $actual);
    }

    public function testTotalImportants()
    {
        $expected = 3;
        $actual = $this->analyzer->getTotalImportants();

        $this->assertEquals($expected, $actual);
    }

    public function testTotalFontStacks()
    {
        $expected = 18;
        $actual = $this->analyzer->getTotalFontStacks();

        $this->assertEquals($expected, $actual);
    }

    public function testUniqueFontStacks()
    {
        $expected = [
            '\'Arial Black\',\'Arial Bold\',Gadget,sans-serif',
            '\'Brush Script MT\',cursive',
            '\'Droid Sans\',serif',
            '\'Noto Sans\'',
            '\'Source Sans Pro\',serif',
            '-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif,\'Apple Color Emoji\',\'Segoe UI Emoji\',\'Segoe UI Symbol\'',
            'Consolas,\'Liberation Mono\',Menlo,Courier,monospace',
            'monospace',
            'sans-serif',
            'serif',
        ];
        $actual = $this->analyzer->getUniqueFontStacks();

        $this->assertEquals($expected, $actual);
        $this->assertContainsOnly('string', $actual);
        $this->assertEquals(10, $this->analyzer->getTotalUniqueFontStacks());
    }

    public function testTotalFontSizes()
    {
        $expected = 16;
        $actual = $this->analyzer->getTotalFontSizes();

        $this->assertEquals($expected, $actual);
    }

    public function testUniqueFontSizes()
    {
        $expected = [
            '11px',
            'small',
            '1em',
            '16px',
            'medium',
            '1.1em',
            '1.2em',
            'large',
            '1.3em',
            'calc(3vw + 1em)',
        ];
        $actual = $this->analyzer->getUniqueFontSizes();

        $this->assertEquals(array_diff($expected, $actual), []);
        $this->assertContainsOnly('string', $actual);
        $this->assertEquals(10, $this->analyzer->getTotalUniqueFontSizes());
    }

    public function testUniqueColors()
    {
        $expected = [
            'Aqua',
            'blue',
            'purple',
            'red',
            'tomato',
            'whitesmoke',
            'rgb(100, 200, 10)',
            'rgba(100, 200, 10, 0.5)',
            'rgba(100, 200, 10, .5)',
            'hsl(100, 20%, 30%)',
            'hsla(100, 20%, 30%, 0.5)',
            '#aaa',
            '#aff034',
        ];
        $actual = $this->analyzer->getUniqueColors();
        $this->assertEquals([], array_diff($expected, $actual));
        $this->assertContainsOnly('string', $actual);
        $this->assertCount(14, $actual);
        $this->assertEquals(14, $this->analyzer->getTotalUniqueColors());
    }

    public function testOccurrencesPerColor()
    {
        $expected = [
            'Aqua' => 1,
            'blue' => 1,
            'purple' => 2,
            'red' => 1,
            'tomato' => 1,
            'whitesmoke' => 1,
            'rgb(100, 200, 10)' => 1,
            'rgba(100, 200, 10, 0.5)' => 1,
            'rgba(100, 200, 10, .5)' => 1,
            'hsl(100, 20%, 30%)' => 1,
            'hsla(100, 20%, 30%, 0.5)' => 1,
            '#aaa' => 1,
            '#aff034' => 1,
        ];
        $actual = $this->analyzer->getOccurrencesPerColor();

        $this->assertEquals([], array_diff_assoc($expected, $actual));
        $this->assertCount(14, $actual);
    }

    public function testUniqueZIndexes()
    {
        $expected = [
            -100,
            -1,
            0,
            1,
            2147483647
        ];
        $actual = $this->analyzer->getUniqueZindexes();

        $this->assertEquals($expected, $actual);
        $this->assertContainsOnly('integer', $actual);
        $this->assertCount(5, $actual);
        $this->assertEquals(5, $this->analyzer->getTotalUniqueZindexes());
    }

    public function testBrowserHacks()
    {
        $expected = [
            '_property: value;',
            'property: value !ie;',
            'property: value\9;',
            '*zoom: 1;'
        ];
        $actual = $this->analyzer->getUniqueBrowserHacks();

        $this->assertEquals($expected, $actual);
        $this->assertContainsOnly('string', $actual);
        $this->assertEquals(5, $this->analyzer->getTotalBrowserHacks());
    }
}
