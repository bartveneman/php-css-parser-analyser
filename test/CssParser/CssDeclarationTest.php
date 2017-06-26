<?php

namespace Wallace;

use Wallace\Models\CssParser\CssDeclaration;
use PHPUnit\Framework\TestCase;

class CssDeclarationTest extends TestCase
{
    public function testGetProperty()
    {
        $declaration = new CssDeclaration('margin: 0;');
        $actual = $declaration->getProperty();
        $expected = 'margin';

        $this->assertEquals($expected, $actual);
    }

    public function testGetValue()
    {
        $declaration = new CssDeclaration('margin: 0');
        $this->assertEquals('0', $declaration->getValue());
    }

    /**
     * @dataProvider prefixedSuccessProvider
     */
    public function testIsPrefixedSuccess($declaration, $expected)
    {
        $this->assertEquals($declaration->isPrefixed(), $expected);
    }

    public function prefixedSuccessProvider()
    {
        return [
            [
                new CssDeclaration('-webkit-border-radius: 3px;'),
                true
            ],
            [
                new CssDeclaration('border-radius: 0'),
                false
            ],
            [
                new CssDeclaration('background: -webkit-linear-gradient(left, red, blue)'),
                true
            ]
        ];
    }

    /**
     * @dataProvider importantSuccessProvider
     */
    public function testIsImportantSuccess($declaration)
    {
        $this->assertTrue($declaration->isImportant());
    }

    public function importantSuccessProvider()
    {
        return [
            [new CssDeclaration('color: blue!important')],
            [new CssDeclaration('color: red !important')],
        ];
    }

    /**
     * @dataProvider importantFailureProvider
     */
    public function testIsImportantFailure($declaration)
    {
        $this->assertFalse($declaration->isImportant());
    }

    public function importantFailureProvider()
    {
        return [
            [new CssDeclaration('color: black')],
            [new CssDeclaration('content: "!important"')],
        ];
    }

    /**
     * @dataProvider isKeywordSuccessProvider
     */
    public function testIsKeywordSuccess($declaration)
    {
        $this->assertTrue($declaration->isKeyword());
    }

    public function isKeywordSuccessProvider()
    {
        return [
            [new CssDeclaration('color: auto')],
            [new CssDeclaration('margin: inherit')],
            [new CssDeclaration('background: initial')],
        ];
    }

    /**
     * @dataProvider isKeywordFailureProvider
     */
    public function testIsKeywordFailure($declaration)
    {
        $this->assertFalse($declaration->isKeyword());
    }

    public function isKeywordFailureProvider()
    {
        return [
            [new CssDeclaration('font-size: whatever')],
            [new CssDeclaration('margin: 0px')],
        ];
    }

    /**
     * @dataProvider fontSizeSuccessProvider
     */
    public function testGetFontSizeSuccess($declaration, $expectedSize)
    {
        $this->assertEquals($expectedSize, $declaration->getFontSize());
    }

    public function fontSizeSuccessProvider()
    {
        return [
            [
                new CssDeclaration('font-size: 10px'),
                '10px'
            ],
            [
                new CssDeclaration('font: 1em sans-serif'),
                '1em'
            ],
            [
                new CssDeclaration('font: normal 1em/1.5 "Open Sans", sans-serif'),
                '1em'
            ],
            [
                new CssDeclaration('font: normal 400 2em/1.2 "Roboto", serif'),
                '2em'
            ],
            [
                new CssDeclaration('font:11px Consolas, "Liberation Mono", Menlo, Courier, monospace;'),
                '11px'
            ],
        ];
    }

    /**
     * @dataProvider fontSizeFailureProvider
     */
    public function testGetFontSizeFailure($declaration)
    {
        $this->assertNull($declaration->getFontSize());
    }

    public function fontSizeFailureProvider()
    {
        return [
            [new CssDeclaration('color: green')],
        ];
    }

    /**
     * @dataProvider getFontStackProvider
     */
    public function testGetFontStack($declaration, $expected)
    {
        $this->assertEquals($declaration->getFontStack(), $expected);
    }

    public function getFontStackProvider()
    {
        return [
            [
                new CssDeclaration('font-family: monospace'),
                'monospace'
            ],
            [
                new CssDeclaration('font-family: -apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif,\'Apple Color Emoji\',\'Segoe UI Emoji\',\'Segoe UI Symbol\''),
                '-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif,\'Apple Color Emoji\',\'Segoe UI Emoji\',\'Segoe UI Symbol\''
            ],
            [
                new CssDeclaration('font-family:Consolas,\'Liberation Mono\',Menlo,Courier,monospace'),
                'Consolas,\'Liberation Mono\',Menlo,Courier,monospace'
            ],
            [
                new CssDeclaration('font: 11px Consolas,\'Liberation Mono\',Menlo,Courier,monospace'),
                'Consolas,\'Liberation Mono\',Menlo,Courier,monospace'
            ],
            [
                new CssDeclaration('font:normal normal 11px/1.5 -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol"'),
                '-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif,\'Apple Color Emoji\',\'Segoe UI Emoji\',\'Segoe UI Symbol\''
            ],
            [
                new CssDeclaration('font-family: Arial,"ヒラギノ角ゴ Pro W3","Hiragino Kaku Gothic Pro",Osaka,"メイリオ",Meiryo,"ＭＳ Ｐゴシック","MS PGothic",sans-serif'),
                'Arial,\'ヒラギノ角ゴ Pro W3\',\'Hiragino Kaku Gothic Pro\',Osaka,\'メイリオ\',Meiryo,\'ＭＳ Ｐゴシック\',\'MS PGothic\',sans-serif'
            ],
        ];
    }

    /**
     * @dataProvider isBrowserHackSuccessProvider
     */
    public function testIsBrowserHackSuccess($declaration)
    {
        $isHack = (new CssDeclaration($declaration))->isBrowserHack();
        $this->assertTrue($isHack);
    }

    public function isBrowserHackSuccessProvider()
    {
        return [
            ['_property: value;'],
            ['-property: value;'],
            ['!property: value;'],
            ['$property: value;'],
            ['&property: value;'],
            ['*property: value;'],
            [')property: value;'],
            ['=property: value;'],
            ['%property: value;'],
            ['+property: value;'],
            ['@property: value;'],
            [',property: value;'],
            ['.property: value;'],
            ['/property: value;'],
            ['`property: value;'],
            [']property: value;'],
            ['#property: value;'],
            ['~property: value;'],
            ['?property: value;'],
            [':property: value;'],
            ['|property: value;'],
            ['property: value !ie;'],
            ['property: value!ie;'],
            ['property: value!canbeanything;'],
            ['property: value\9;'],
        ];
    }

    /**
     * @dataProvider isBrowserHackFailureProvider
     */
    public function testIsBrowserhackFailure($declaration)
    {
        $isHack = (new CssDeclaration($declaration))->isBrowserHack();
        $this->assertFalse($isHack);
    }

    public function isBrowserHackFailureProvider()
    {
        return [
            ['background-color:rgba(51,51,51,0.8)'],
        ];
    }
}
