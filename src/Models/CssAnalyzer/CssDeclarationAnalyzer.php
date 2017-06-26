<?php

namespace Wallace\Models\CssAnalyzer;

class CssDeclarationAnalyzer implements CssAnalyzerInterface
{
    const COLOR_KEYWORDS = [
        'AliceBlue','AntiqueWhite','Aqua','Aquamarine','Azure',
        'Beige','Bisque','Black','BlanchedAlmond','Blue','BlueViolet','Brown',
        'BurlyWood',
        'CadetBlue','Chartreuse','Chocolate','Coral','CornflowerBlue','Cornsilk',
        'Crimson','Cyan',
        'DarkBlue','DarkCyan','DarkGoldenRod','DarkGray','DarkGreen','DarkKhaki',
        'DarkMagenta','DarkOliveGreen','DarkOrange','DarkOrchid','DarkRed',
        'DarkSalmon','DarkSeaGreen','DarkSlateBlue','DarkSlateGray','DarkTurquoise',
        'DarkViolet','DeepPink','DeepSkyBlue','DimGray','DodgerBlue',
        'FireBrick','FloralWhite','ForestGreen','Fuchsia',
        'Gainsboro','GhostWhite','Gold','GoldenRod','Gray','Green','GreenYellow',
        'HoneyDew','HotPink',
        'IndianRed','Indigo','Ivory',
        'Khaki',
        'Lavender','LavenderBlush','LawnGreen','LemonChiffon','LightBlue',
        'LightCoral','LightCyan','LightGoldenRodYellow','LightGray','LightGreen',
        'LightPink','LightSalmon','LightSeaGreen','LightSkyBlue','LightSlateGray',
        'LightSteelBlue','LightYellow','Lime','LimeGreen','Linen',
        'Magenta','Maroon','MediumAquaMarine','MediumBlue','MediumOrchid',
        'MediumPurple','MediumSeaGreen','MediumSlateBlue','MediumSpringGreen',
        'MediumTurquoise','MediumVioletRed','MidnightBlue','MintCream','MistyRose',
        'Moccasin',
        'NavajoWhite','Navy',
        'OldLace',
        'Olive','OliveDrab','Orange','OrangeRed','Orchid',
        'PaleGoldenRod','PaleGreen','PaleTurquoise','PaleVioletRed','PapayaWhip',
        'PeachPuff','Peru','Pink','Plum','PowderBlue','Purple',
        'RebeccaPurple','Red','RosyBrown','RoyalBlue',
        'SaddleBrown','Salmon','SandyBrown','SeaGreen','SeaShell','Sienna','Silver',
        'SkyBlue','SlateBlue','SlateGray','Snow','SpringGreen','SteelBlue',
        'Tan','Teal','Thistle','Tomato','Turquoise',
        'Violet',
        'Wheat','WhiteSmoke','White',
        'Yellow','YellowGreen'
    ];

    const PROPERTY_ZINDEX = 'z-index';
    const PROPERTY_FONT = 'font';
    const PROPERTY_FONT_SIZE = 'font-size';
    const PROPERTY_FONT_FAMILY = 'font-family';

    const EMPTY_STRING = '';

    private $importants;
    private $prefixed;
    private $font_sizes;
    private $unique_font_sizes;
    private $font_stacks;
    private $unique_font_stacks;
    private $colors;
    private $zindexes;
    private $unique_zindexes;
    private $browser_hacks;

    public function __construct($declarations)
    {
        $this->declarations = $declarations;
        $this->setImportants($declarations);
        $this->setPrefixed($declarations);
        $this->setFontSizes($declarations);
        $this->setFontStacks($declarations);
        $this->setZIndexes($declarations);
        $this->setBrowserHacks($declarations);
        $this->setColors($declarations);
    }

    public function getTotalDeclarations()
    {
        return count($this->declarations);
    }

    private function setImportants($declarations)
    {
        $this->importants = array_filter($declarations, function ($declaration) {
            return $declaration->isImportant();
        });
    }

    public function getTotalImportants()
    {
        return count($this->importants);
    }

    private function setPrefixed($declarations)
    {
        $this->prefixed = array_filter($declarations, function ($declaration) {
            return $declaration->isPrefixed();
        });
    }

    public function getTotalPrefixedDeclarations()
    {
        return count($this->prefixed);
    }

    public function getPrefixedDeclarationsShare()
    {
        $declarations_count = $this->getTotalDeclarations();
        $prefixed_count = $this->getTotalPrefixedDeclarations();

        if ($declarations_count === 0) {
            return 0; // Catch DivideByZeroException
        }

        return $prefixed_count / $declarations_count;
    }

    private function setFontSizes($declarations)
    {
        $font_sizes = [];

        foreach ($declarations as $declaration) {
            if ($declaration->getProperty() === self::PROPERTY_FONT_SIZE && !$declaration->isKeyword()) {
                $font_sizes[] = $declaration->getValue();
            } elseif ($declaration->getProperty() === self::PROPERTY_FONT) {
                if ($declaration->getFontSize() && !$declaration->isKeyword()) { // Catch NULL or ''
                    $font_sizes[] = $declaration->getFontSize();
                }
            }
        }

        usort($font_sizes, function ($a, $b) {
            return strnatcasecmp($a, $b);
        });

        $this->font_sizes = $font_sizes;
        $this->unique_font_sizes = array_values(array_unique($font_sizes));
    }

    public function getTotalFontSizes()
    {
        return count($this->font_sizes);
    }

    public function getTotalUniqueFontSizes()
    {
        return count($this->unique_font_sizes);
    }

    public function getUniqueFontSizes()
    {
        return CssFontSizeSorter::sort($this->unique_font_sizes);
    }

    private function setFontStacks($declarations)
    {
        $stacks = [];

        foreach ($declarations as $declaration) {
            $stack = $declaration->getFontStack();

            if ($stack && !$declaration->isKeyword()) {
                $stacks[] = $stack;
            }
        }

        usort($stacks, function ($a, $b) {
            return strnatcasecmp($a, $b);
        });

        $this->font_stacks = $stacks;
        $this->unique_font_stacks = array_values(array_unique($stacks));

        return $this;
    }

    public function getTotalFontStacks()
    {
        return count($this->font_stacks);
    }

    public function getUniqueFontSTacks()
    {
        return $this->unique_font_stacks;
    }

    public function getTotalUniqueFontStacks()
    {
        return count($this->unique_font_stacks);
    }

    private function setColors($declarations)
    {
        $colors = [];
        $regex_keyword_negate = '(?![-\'\"_\.])';
        $reg_key = $regex_keyword_negate . '(' . implode('|', self::COLOR_KEYWORDS) . ')' . $regex_keyword_negate;
        $reg_hex = '#[a-f0-9]{3,6}';
        $reg_rgb = 'rgb\(\d{1,3},\s*\d{1,3},\s*\d{1,3}\)';
        $reg_rgba = 'rgba\(\d{1,3},\s*\d{1,3},\s*\d{1,3},\s*\d*(?:\.\d+)?\)';
        $reg_hsl = 'hsl\(\d{1,3},\s*\d{1,3}%,\s*\d{1,3}%\)';
        $reg_hsla = 'hsla?\(\d{1,3},\s*\d{1,3}%,\s*\d{1,3}%,\s*\d*(?:\.\d+)?\)';
        $reg = '/('.$reg_hex.')|('.$reg_rgb.')|('.$reg_rgba.')|('.$reg_hsl.')|('.$reg_hsla.')|'.$reg_key.'/i';

        foreach ($declarations as $declaration) {
            if ($declaration->isKeyword()) {
                continue;
            }

            $value = $declaration->getValue();

            preg_match_all($reg, $value, $matches);

            if (count($matches) > 0) {
                foreach ($matches[0] as $color) {
                    if ($color !== self::EMPTY_STRING) {
                        if (!array_key_exists($color, $colors)) {
                            $colors[$color] = 0;
                        }
                        $colors[$color] += 1;
                    }
                }
            }
        }

        ksort($colors);
        $this->colors = $colors;
        return $this;
    }

    public function getUniqueColors()
    {
        $colors = array_values(
            array_unique(
                array_keys($this->colors)
            )
        );

        return $colors;
    }

    public function getOccurrencesPerColor()
    {
        return $this->colors;
    }

    public function getTotalUniqueColors()
    {
        return count($this->getUniqueColors());
    }

    public function setZIndexes($declarations)
    {
        $zindexes = [];

        foreach ($declarations as $declaration) {
            if ($declaration->getProperty() === self::PROPERTY_ZINDEX) {
                if (!$declaration->isKeyword()) {
                    $zindexes[] = intval($declaration->getValue());
                }
            }
        }

        sort($zindexes, SORT_NUMERIC);

        $this->zindexes = $zindexes;
        $this->unique_zindexes = array_values(array_unique($zindexes));
    }


    public function getTotalZIndexes()
    {
        return count($this->zindexes);
    }

    public function getUniqueZindexes()
    {
        return $this->unique_zindexes;
    }

    public function getTotalUniqueZIndexes()
    {
        return count($this->unique_zindexes);
    }

    private function setBrowserHacks($declarations)
    {
        $this->browser_hacks = array_filter($declarations, function ($declaration) {
            if ($declaration->isBrowserHack()) {
                return $declaration;
            }
        });
    }

    public function getUniqueBrowserHacks()
    {
        return array_values(
            array_unique(
                array_map(function ($declaration) {
                    return (string)$declaration;
                }, $this->browser_hacks)
            )
        );
    }

    public function getTotalBrowserHacks()
    {
        return count($this->browser_hacks);
    }

    public function analyze()
    {
        return [
            'total_declarations' => $this->getTotalDeclarations(),
            'total_prefixed_declarations' => $this->getTotalPrefixedDeclarations(),
            'prefixed_declarations_share' => $this->getPrefixedDeclarationsShare(),
            'total_importants' => $this->getTotalImportants(),
            'total_font_sizes' => $this->getTotalFontSizes(),
            'unique_font_sizes' => $this->getUniqueFontSizes(),
            'total_unique_font_sizes' => $this->getTotalUniqueFontSizes(),
            'total_font_stacks' => $this->getTotalFontStacks(),
            'unique_font_stacks' => $this->getUniqueFontSTacks(),
            'total_unique_font_stacks' => $this->getTotalUniqueFontStacks(),
            'unique_colors' => $this->getUniqueColors(),
            'total_unique_colors' => $this->getTotalUniqueColors(),
            'occurrences_per_color' => $this->getOccurrencesPerColor(),
            'total_zindexes' => $this->getTotalZIndexes(),
            'unique_zindexes' => $this->getUniqueZindexes(),
            'total_unique_zindexes' => $this->getTotalUniqueZIndexes(),
            'browser_declaration_hacks' => $this->getUniqueBrowserHacks(),
            'total_browser_declaration_hacks' => $this->getTotalBrowserHacks()
        ];
    }
}
