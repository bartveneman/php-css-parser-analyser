<?php

namespace Wallace\Models\CssParser;

class CssDeclaration
{
    const KEYWORDS = [
        'inherit',
        'initial',
        'auto',
    ];

    const BROWSER_HACK_PROPERTY_PREFIXES = '! $ & * ( ) = % + @ , . / ` [ ] # ~ ? : < > | - _';
    const BROWSER_HACKS_VALUE = [
        '/\\\\9/',
        '/\!(?!important)\w+/',
    ];
    const IS_PREFIXED_REGEX = '/^-(?:webkit|moz|ms|o)-/i';

    const SINGLE_QUOTE = '\'';
    const DOUBLE_QUOTE = '"';
    const FORWARD_SLASH = '/';
    const COMMA = ',';
    const COLON = ':';
    const SPACE_AROUND_COMMA_REGEX = '/\s*,\s*/';
    const SPACE_BEFORE_SLASH_REGEX = '/\s\//';
    const SPACE_AFTER_SLASH_REGEX = '/\/\s/';
    const IMPORTANT_REGEX = '/\s*!important$/';
    const CONTENT_BEFORE_FONT_STACK_REGEX = '/([a-zA-Z0-9-]+,.*)|(\')/';


    const FONT_STYLE_KEYWORDS = ['italic', 'oblique'];
    const FONT_VARIANT_KEYWORDS = ['small-caps'];
    const FONT_WEIGHT_KEYWORDS = [
        'bold',
        'bolder',
        'lighter',
        '100',
        '200',
        '300',
        '400',
        '500',
        '600',
        '700',
        '800',
        '900',
    ];
    const FONT_SIZE_KEYWORDS = [
        'xx-small',
        'x-small',
        'small',
        'medium',
        'large',
        'x-large',
        'xx-large',
        'larger',
        'smaller',
    ];

    private $value;
    private $property;
    private $isImportant = false;
    private $font;

    public function __construct($raw)
    {
        $this->raw = $raw;
        $this->setProperty();
        $this->setValue();

        if ($this->property === 'font') {
            $this->font = $this->getExpandedFontShorthand();
        }
    }

    private function setProperty()
    {
        if (strpos($this->raw, self::COLON) === false) {
            return;
        }

        $leading_semicolon = false;

        // Strip the leading : for old IE Hacks
        if (strpos($this->raw, self::COLON) === 0) {
            $this->raw = substr($this->raw, 1);
            $leading_semicolon = true;
        }

        $this->property = trim(explode(self::COLON, $this->raw)[0]);

        if ($leading_semicolon) {
            $this->property = self::COLON . $this->property;
        }

        return $this;
    }

    private function setValue()
    {
        if (strpos($this->raw, self::COLON) === false) {
            return;
        }

        $this->value = trim(explode(self::COLON, $this->raw)[1]);

        if (preg_match(self::IMPORTANT_REGEX, $this->value) === 1) {
            $this->value = preg_replace(self::IMPORTANT_REGEX, '', $this->value);
            $this->isImportant = true;
        }

        return $this;
    }

    public function getProperty()
    {
        return $this->property;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function isImportant()
    {
        return $this->isImportant;
    }

    public function isPrefixed()
    {
        return preg_match(self::IS_PREFIXED_REGEX, $this->property) === 1
            || preg_match(self::IS_PREFIXED_REGEX, $this->value) === 1;
    }

    public function isBrowserHack()
    {
        $prefixes = explode(' ', self::BROWSER_HACK_PROPERTY_PREFIXES);

        // Check if the declaration starts with a prefix
        $first_char = $this->property[0];
        if (in_array($first_char, $prefixes) && !$this->isPrefixed()) {
            return true;
        }

        // Check if a declaration ends with a postfix
        foreach (self::BROWSER_HACKS_VALUE as $hack) {
            if (preg_match($hack, $this->value) === 1) {
                return true;
            }
        }

        return false;
    }

    public function isKeyword()
    {
        return in_array($this->value, self::KEYWORDS);
    }

    /**
     * http://stackoverflow.com/questions/6708051/convert-css-font-shorthand-to-long-hand
     */
    private function getExpandedFontShorthand()
    {
        if ($this->property !== 'font') {
            return false;
        }

        // Format the font string for easier parsing
        $fontString = $this->formatFontStack($this->value);

        // Split $fontString. The only area where quotes should be found is around
        // font-families, which are at the end.
        $parts = preg_split(
            self::CONTENT_BEFORE_FONT_STACK_REGEX,
            $fontString,
            2,
            PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY
        );
        $chunks = preg_split('/ /', $parts[0], null, PREG_SPLIT_NO_EMPTY);

        if (isset($parts[1])) {
            if (isset($parts[2])) {
                $chunks[] = $parts[1] . $parts[2];
            } else {
                $chunks[] = $parts[1];
            }
        }

        $font = [];
        $next = -1;

        // Manage font-style / font-variant / font-weight / font-size properties
        $possibleProperties = [];

        // First pass on chunks 0 to 2 to see what each can be
        for ($i = 0; $i < 3; $i += 1) {
            $possibleProperties[$i] = [];

            if (!isset($chunks[$i])) {
                if ($next === -1) {
                    $next = $i;
                }

                continue;
            }

            if (in_array($chunks[$i], self::FONT_STYLE_KEYWORDS)) {
                $possibleProperties[$i] = 'font-style';
            } elseif (in_array($chunks[$i], self::FONT_VARIANT_KEYWORDS)) {
                $possibleProperties[$i] = 'font-variant';
            } elseif (in_array($chunks[$i], self::FONT_WEIGHT_KEYWORDS)) {
                $possibleProperties[$i] = 'font-weight';
            } elseif ($chunks[$i] === 'normal') {
                $possibleProperties['normal'] = 1;
            } elseif ($next === -1) {
                // Used to know where other properties will start at
                $next = $i;
            }
        }

        // Second pass to determine what real properties are defined
        for ($i = 0; $i < 3; $i += 1) {
            if (!empty($possibleProperties[$i])) {
                $font[$possibleProperties[$i]] = $chunks[$i];
            }
        }

        // Third pass to set the properties which have to be set as "normal"
        if (!empty($possibleProperties['normal'])) {
            $properties = ['font-style', 'font-variant', 'font-weight'];

            foreach ($properties as $property) {
                if (!isset($font[$property])) {
                    $font[$property] = 'normal';
                }
            }
        }

        if (!isset($chunks[$next])) {
            return $font;
        }

        // Extract font-size and line height
        if (strpos($chunks[$next], '/')) {
            $size = explode('/', $chunks[$next]);
            $font['font-size'] = $size[0];
        }

        // Extract font-size and line height
        if (strpos($chunks[$next], '/')) {
            $size = explode('/', $chunks[$next]);
            $font['font-size'] = $size[0];
            $font['line-height'] = $size[1];
            $font['font-family'] = $chunks[$next + 1];
        } elseif (preg_match('`^-?[0-9]+`', $chunks[$next]) ||
            in_array($chunks[$next], self::FONT_SIZE_KEYWORDS)) {
            $font['font-size'] = $chunks[$next];
            $font['font-family'] = $chunks[$next + 1];
        } else {
            $font['font-family'] = $chunks[$next];
        }

        return $font;
    }

    private function formatFontStack($unformatted)
    {
        // Convert all double quotes to single quotes
        $formatted = str_replace('"', '\'', $unformatted);
        // Remove all spaces around commas
        $formatted = preg_replace(self::SPACE_AROUND_COMMA_REGEX, self::COMMA, $formatted);

        // Trim whitespace around the forward slash that separates font-size
        // and line-height; font: 14 / 1 serif;
        $formatted = preg_replace(self::SPACE_BEFORE_SLASH_REGEX, self::FORWARD_SLASH, $formatted);
        $formatted = preg_replace(self::SPACE_AFTER_SLASH_REGEX, self::FORWARD_SLASH, $formatted);

        return $formatted;
    }

    public function getFontStack()
    {
        if ($this->property === 'font-family') {
            return $this->formatFontStack($this->getValue());
        }

        if (is_array($this->font) && array_key_exists('font-family', $this->font)) {
            return $this->font['font-family'];
        }

        return null;
    }

    public function getFontSize()
    {
        if ($this->property === 'font-size') {
            return $this->getValue();
        }

        if ($this->property === 'font' && array_key_exists('font-size', $this->font)) {
            return $this->font['font-size'];
        }

        return null;
    }

    public function __toString()
    {
        return $this->property . ': ' . $this->value . ';';
    }
}
