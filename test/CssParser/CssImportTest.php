<?php

namespace Wallace;

use Wallace\Models\CssParser\CssImport;
use PHPUnit\Framework\TestCase;

class CssImportTest extends TestCase
{
    /**
     * @dataProvider urlSuccessProvider
     */
    public function testGetUrlSuccess($import, $expected)
    {
        $actual = (new CssImport($import))->getUrl();
        $this->assertEquals($actual, $expected);
    }

    public function urlSuccessProvider()
    {
        return [
            [
                '@import url("fineprint.css") print;',
                'url("fineprint.css")'
            ],
            [
                '@import url("bluish.css") projection, tv;',
                'url("bluish.css")'
            ],
            [
                '@import \'custom.css\';',
                '\'custom.css\''
            ],
            [
                '@import url("chrome://communicator/skin/");',
                'url("chrome://communicator/skin/")'
            ],
            [
                '@import "common.css" screen, projection;',
                '"common.css"'
            ],
            [
                '@import url(\'landscape.css\') screen and (orientation:landscape);',
                'url(\'landscape.css\')'
            ],
        ];
    }

    /**
     * @dataProvider mediaQuerySuccessProvider
     */
    public function testGetMediaQuerySuccess($import, $expected)
    {
        $actual = (new CssImport($import))->getMediaQuery();
        $this->assertEquals($actual, $expected);
    }

    /**
     * @dataProvider mediaQuerySuccessProvider
     */
    public function testHasMediaQuerySuccess($import, $query)
    {
        $actual = (new CssImport($import))->hasMediaQuery();
        $this->assertTrue($actual);
    }

    public function mediaQuerySuccessProvider()
    {
        return [
            [
                '@import url("fineprint.css") print;',
                'print'
            ],
            [
                '@import url("bluish.css") projection, tv;',
                'projection, tv'
            ],
            [
                '@import "common.css" screen, projection;',
                'screen, projection'
            ],
            [
                '@import url(\'landscape.css\') screen and (orientation:landscape);',
                'screen and (orientation:landscape)'
            ],
        ];
    }

    /**
     * @dataProvider mediaQueryFailureProvider
     */
    public function testHasMediaQueryFailure($import)
    {
        $actual = (new CssImport($import))->hasMediaQuery();
        $this->assertFalse($actual);
    }

    public function mediaQueryFailureProvider()
    {
        return [
            ['@import \'custom.css\';'],
            ['@import \'https://google.com\';'],
        ];
    }
}
