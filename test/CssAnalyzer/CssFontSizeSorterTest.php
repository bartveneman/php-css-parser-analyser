<?php

namespace Wallace;

use Wallace\Models\CssAnalyzer\CssFontSizeSorter;
use PHPUnit\Framework\TestCase;

class CssFontSizeSorterTest extends TestCase
{
    public function testSortSuccess()
    {
        $font_sizes = [
            '1ex',
            '1ch',
            '1cm',
            '1mm',
            '1in',
            '1pc',
            '1pt',
            '11px',
            '9px',
            '10px',
            'xx-small',
            '3em',
            '120%',
            'x-large',
            'x-small',
            '9px',
            '2pc',
        ];
        $expected = [
            '1pt',
            '1mm',
            '1pc',
            '2pc',
            '1ex',
            '1cm',
            '9px',
            'xx-small',
            '10px',
            '11px',
            '1ch',
            'x-small',
            '120%',
            '1in',
            'x-large',
            '3em',
        ];
        $sorter = new CssFontSizeSorter();
        $actual = $sorter->sort($font_sizes);

        $this->assertEquals($expected, $actual);
        $this->assertCount(16, $actual);
    }

    public function testEqualities()
    {
        $actual = CssFontSizeSorter::sort(['1rem', '16px', '100%', '1em', 'medium']);
        $expected = ['1rem', '16px', '100%', '1em', 'medium'];

        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider fontSizeToPxSuccessProvider
     */
    public function testFontSizeToPxSuccess($size, $floated, $expected)
    {
        $actual = CssFontSizeSorter::fontSizeToPx($size);
        $actualFloated = CssFontSizeSorter::fontSizeToPx($floated);

        $this->assertEquals($expected, $actual);
        $this->assertEquals($expected, $actualFloated);
    }

    public function fontSizeToPxSuccessProvider()
    {
        return [
            ['16px', '16.0px', 16],
            ['1em', '1.0em', 16],
            ['.1em', '0.1em', 1.6],
            ['1.25em', '1.25em', 20],
            ['medium', 'medium', 16],
            ['125%', '125.0%', 20],
            ['10%', '10.0%', 1.6],
            ['1in', '1.0in', (16 * (192/138))],
            ['1ch', '1.0ch', (16 * (94.93/138))],
            ['1cm', '1.0cm', (16 * (75.5781/138))],
            ['1ex', '1.0ex', (16 * (73.4/138))],
            ['1pc', '1.0pc', (16 * (32/138))],
            ['1mm', '1.0mm', (16 * (7.54688/138))],
            ['1pt', '1.0pt', (16 * (2.6565/138))],
            ['large', 'large', (16 * 6/5)],
            ['1yolo', '1.0yolo', 1024],
            ['calc(1em + 1px)', 'calc(1.0em + 1.0px)', 1024],
        ];
    }
}
