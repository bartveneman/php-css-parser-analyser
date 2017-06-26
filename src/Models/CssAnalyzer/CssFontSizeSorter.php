<?php

namespace Wallace\Models\CssAnalyzer;

use Wallace\Models\CssParser\CssDeclaration;

class CssFontSizeSorter
{
    const BASE_FONT_SIZE = 16;

    const SIZE_RATIOS = [
        '%'  => (1/100),
        'pt' => (2.6565/138),
        'mm' => (7.54688/138),
        'pc' => (32/138),
        'ex' => (73.4/138),
        'cm' => (75.5781/138),
        'ch' => (94.93/138),
        'em' => 1,
        'in' => (192/138),
    ];

    public static function fontSizeToPx($font_size)
    {
        if (preg_match('/px$/', $font_size)) {
            return (float)$font_size;
        }

        // Ems *and* Rems
        if (preg_match('/em$/', $font_size)) {
            return (float)$font_size * self::BASE_FONT_SIZE * self::SIZE_RATIOS['em'];
        }

        if (preg_match('/%$/', $font_size)) {
            return (float)$font_size * self::BASE_FONT_SIZE * self::SIZE_RATIOS['%'];
        }

        if (preg_match('/in$/', $font_size)) {
            return (float)$font_size * self::BASE_FONT_SIZE * self::SIZE_RATIOS['in'];
        }

        if (preg_match('/ch$/', $font_size)) {
            return (float)$font_size * self::BASE_FONT_SIZE * self::SIZE_RATIOS['ch'];
        }

        if (preg_match('/cm$/', $font_size)) {
            return (float)$font_size * self::BASE_FONT_SIZE * self::SIZE_RATIOS['cm'];
        }

        if (preg_match('/ex$/', $font_size)) {
            return (float)$font_size * self::BASE_FONT_SIZE * self::SIZE_RATIOS['ex'];
        }

        if (preg_match('/pc$/', $font_size)) {
            return (float)$font_size * self::BASE_FONT_SIZE * self::SIZE_RATIOS['pc'];
        }

        if (preg_match('/mm$/', $font_size)) {
            return (float)$font_size * self::BASE_FONT_SIZE * self::SIZE_RATIOS['mm'];
        }

        if (preg_match('/pt$/', $font_size)) {
            return (float)$font_size * self::BASE_FONT_SIZE * self::SIZE_RATIOS['pt'];
        }

        // https://drafts.csswg.org/css-fonts-3/#font-size-prop
        switch ($font_size) {
            case 'inherit':
            case 'auto':
            case 'initial':
                return self::BASE_FONT_SIZE;
            case 'xx-small':
                return self::BASE_FONT_SIZE * 3/5;
            case 'x-small':
                return self::BASE_FONT_SIZE * 3/4;
            case 'small':
            case 'smaller':
                return self::BASE_FONT_SIZE * 8/9;
            case 'medium':
                return self::BASE_FONT_SIZE;
            case 'large':
            case 'larger':
                return self::BASE_FONT_SIZE * 6/5;
            case 'x-large':
                return self::BASE_FONT_SIZE * 3/2;
            case 'xx-large':
                return self::BASE_FONT_SIZE * 2/1;
            default:
                // Will contain calc() and viewport units
                return 1024;
        }
    }

    public static function sort(array $font_sizes)
    {
        $sizes = [];
        foreach ($font_sizes as $size) {
            $sizes[$size] = self::fontSizeToPx($size);
        }

        asort($sizes);
        return array_keys($sizes);
    }
}
