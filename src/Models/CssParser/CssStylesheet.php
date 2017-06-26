<?php

namespace Wallace\Models\CssParser;

class CssStylesheet
{
    const COMMENT_REGEX = '/\/\*.+?\*\//';
    const WHITESPACE_REGEX = '/\s\s+/';
    const SELECTOR_BLOCK_REGEX = '/^[^\{]+/';

    const EMPTY_STRING = '';
    const SINGLE_SPACE = ' ';
    const OPENING_PARENTHESIS = '{';
    const CLOSING_PARENTHESIS = '}';

    private $raw;
    private $children;

    public function __construct($raw)
    {
        $this->raw = $raw;
        $this->children = $this->getChildren($this->raw);
    }

    public function getRules()
    {
        return array_filter($this->children, function ($child) {
            return $this->isRule($child);
        });
    }

    public function getMediaQueries()
    {
        return array_filter($this->children, function ($child) {
            return $this->isMediaQuery($child);
        });
    }

    public function getKeyframes()
    {
        return array_filter($this->children, function ($child) {
            return $this->isKeyframe($child);
        });
    }

    public function getImports()
    {
        return array_filter($this->children, function ($child) {
            return $this->isImport($child);
        });
    }

    public function getSupportsRules()
    {
        return array_filter($this->children, function ($child) {
            return $this->isSupportsRule($child);
        });
    }

    public function getMalformedStatements()
    {
        return array_filter($this->children, function ($child) {
            return $this->isMalformedStatement($child);
        });
    }

    private function getChildren($raw)
    {
        $children = [];
        $depth = 0;
        $child = self::EMPTY_STRING;
        $stylesheet = $this->stripComments($this->stripFormatting($raw));
        $tokens = str_split($stylesheet);

        foreach ($tokens as $token) {
            $child .= $token;

            if ($token === self::OPENING_PARENTHESIS) {
                $depth += 1;
            } elseif ($token === self::CLOSING_PARENTHESIS) {
                $depth -= 1;
            }

            if ($depth === 0 && preg_match('/\}|;/', $token) === 1) {
                $children[] = trim($child);
                $child = self::EMPTY_STRING;
            }
        }

        return $children;
    }

    private function stripComments($string)
    {
        return preg_replace(self::COMMENT_REGEX, self::EMPTY_STRING, $string);
    }

    private function stripFormatting($string)
    {
        return $this->stripNewlines($this->trimWhitespace($string));
    }

    private function trimWhitespace($string)
    {
        return preg_replace(self::WHITESPACE_REGEX, self::SINGLE_SPACE, $string);
    }

    private function stripNewlines($string)
    {
        return str_replace(PHP_EOL, self::EMPTY_STRING, $string);
    }

    private function isRule($string)
    {
        return !$this->isMediaQuery($string)
        && !$this->isKeyframe($string)
        && !$this->isSupportsRule($string)
        && $this->hasRuleBlock($string)
        && $this->hasSelectorBlock($string);
    }

    private function isMalformedStatement($string)
    {
        return !$this->isRule($string)
        && !$this->isMediaQuery($string)
        && !$this->isKeyframe($string)
        && !$this->isImport($string)
        && !$this->isSupportsRule($string);
    }

    private function hasRuleBlock($string)
    {
        return strpos($string, self::OPENING_PARENTHESIS) !== false
        && strpos($string, self::CLOSING_PARENTHESIS) !== false;
    }

    private function hasSelectorBlock($string)
    {
        return preg_match(self::SELECTOR_BLOCK_REGEX, $string) === 1;
    }

    private function isMediaQuery($string)
    {
        return preg_match(CssMediaQuery::MEDIA_QUERY_REGEX, $string) === 1;
    }

    private function isKeyframe($string)
    {
        return preg_match(CssKeyframe::KEYFRAMES_REGEX, $string) === 1;
    }

    private function isImport($string)
    {
        return preg_match(CssImport::IMPORT_REGEX, $string) === 1;
    }

    private function isSupportsRule($string)
    {
        return preg_match(CssSupportsRule::SUPPORTS_REGEX, $string) === 1;
    }
}
