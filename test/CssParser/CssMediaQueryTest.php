<?php

namespace Wallace;

use Wallace\Models\CssParser\CssMediaQuery;
use PHPUnit\Framework\TestCase;

class CssMediaQueryTest extends TestCase
{
    public function testConstructor()
    {
        $mq = new CssMediaQuery('@media print {}');
        $this->assertInstanceOf('Wallace\Models\CssParser\CssMediaQuery', $mq);
    }

    /**
     * @dataProvider getQueriesProvider
     */
    public function testGetQueries($mq, $expectedQueries)
    {
        $this->assertEquals($mq->getQueries(), $expectedQueries);
        $this->assertContainsOnly('string', $mq->getQueries());
    }

    public function getQueriesProvider()
    {
        return [
            [
                new CssMediaQuery('@media print {}'),
                [
                  '@media print'
                ]
            ],
            [
                new CssMediaQuery('@media all and (min-width: 33em) {}'),
                [
                  '@media all and (min-width: 33em)'
                ]
            ],
            [
                new CssMediaQuery('@media (min-width: 33em) { @media print {} }'),
                [
                  '@media (min-width: 33em)',
                  '@media print'
                ]
            ],
            [
                new CssMediaQuery('@media(min-width:33em){}'),
                [
                  '@media(min-width:33em)'
                ]
            ],
            [
                new CssMediaQuery('@-moz-document url-prefix(){}'),
                [
                  '@-moz-document url-prefix()'
                ]
            ],
            [
                new CssMediaQuery('@media screen and (-ms-high-contrast: active), (-ms-high-contrast: none) {}'),
                [
                  '@media screen and (-ms-high-contrast: active), (-ms-high-contrast: none)'
                ]
            ],
            [
                new CssMediaQuery('@media screen and (min-width: 0\0) {}'),
                [
                  '@media screen and (min-width: 0\0)'
                ]
            ]
        ];
    }

    /**
     * @dataProvider browserHacksProvider
     */
    public function testBrowserHacks($mq)
    {
        $this->assertTrue($mq->isBrowserHack());
    }

    public function browserHacksProvider()
    {
        return [
            [new CssMediaQuery('@media screen and (min-width: 0\0) {}')],
            [new CssMediaQuery('@media screen and (-ms-high-contrast: active), (-ms-high-contrast: none) {}')],
            [new CssMediaQuery('@-moz-document url-prefix(){}')],
        ];
    }

    /**
     * @dataProvider getRulesInheritenceProvider
     */
    public function testGetRulesInheritence($rule, $expected)
    {
        $media_query = new CssMediaQuery($rule);
        $this->assertEquals($expected, $media_query->getRules());
    }

    public function getRulesInheritenceProvider()
    {
        return [
            [
                '@media screen { .foo { color: green } }',
                [
                  '.foo { color: green }'
                ]
            ],
        ];
    }
}
