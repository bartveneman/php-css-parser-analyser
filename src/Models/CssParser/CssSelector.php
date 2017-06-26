<?php

namespace Wallace\Models\CssParser;

class CssSelector
{

    const IDENTIFIER_DELIMITERS = '.|#|>|[| |:|*';
    const COLON = ':';
    const PAREN_OPEN = '(';
    const PAREN_CLOSE = ')';
    const BRACKET_OPEN = '[';
    const BRACKET_CLOSE = ']';
    const BRACKETS = '[]';
    const EMPTY_STRING = '';
    const SPACE = ' ';
    const BACKSLASH = '\\';
    const BROWSER_HACK_REGEXES = [
        '/^\*\s?+\s?html/',
        '/^\*\:first-child\s?\+\s?html/',
        '/^\*\s?\+\s?html/',
        '/^body\*.+/',
        '/^html\s?>\s?body/',
        '/\\$/',
        '/^\:root\s/',
        '/\bbody:first-child\b/',
        '/\bbody:last-child\b/',
        '/body:nth-of-type\(1\)/',
        '/\bbody:first-of-type\b/',
        '/:not\(\[attr\*\=[\"\\\']{2}]\)/',
        '/:not\(\*:root\)/',
        '/^body:empty/',
        '/x:-moz-any-link/',
        '/body:not\(:-moz-handler-blocked\)/',
        '/_::selection/',
        '/x:-IE7/',
        '/html:first-child/',
        '/html\[xmlns\*=[\'\"]{2}]/',
        '/_::?-(?:moz|o|ms)-/'
    ];

    private $selector;
    private $specificity;
    private $identifiers = [];

    public function __construct($raw)
    {
        $this->selector = $raw;
        $this->specificity = new CssSpecificity($raw);
        $this->setIdentifiers($raw);
    }

    public function isBrowserHack()
    {
        foreach (self::BROWSER_HACK_REGEXES as $regex) {
            if (preg_match($regex, $this->selector)) {
                return true;
            }
        }

        if (substr($this->selector, -1, 1) === self::BACKSLASH) {
            return true;
        }

        return false;
    }

    public function getSelector()
    {
        return $this->selector;
    }

    public function getSpecificity()
    {
        return $this->specificity->getSpecificity();
    }

    public function getTotalIdentifiers()
    {
        return count($this->identifiers);
    }

    public function getIdentifiers()
    {
        return $this->identifiers;
    }

    private function setIdentifiers($raw)
    {
        $identifier = self::EMPTY_STRING;
        $bracketDepth = 0;
        $parenDepth = 0;
        $chars = str_split($raw);

        for ($index = 0; $index < count($chars); $index++) {
            $char = $chars[$index];
            $insideBrackets = $bracketDepth || $parenDepth;
            $isSecondColon = $index > 0 && $char === self::COLON && $chars[$index - 1] === self::COLON;

            if (!$insideBrackets && $this->isDelimiter($char) && !$isSecondColon) {
                $this->identifiers[] = $identifier;
                $identifier = self::EMPTY_STRING;
            }

            switch ($char) {
                case self::PAREN_OPEN:
                    $parenDepth++;
                    break;
                case self::PAREN_CLOSE:
                    $parenDepth--;
                    break;
                case self::BRACKET_OPEN:
                    $bracketDepth++;
                    break;
                case self::BRACKET_CLOSE:
                    $bracketDepth--;
                    break;
            }

            if (!in_array($char, [self::SPACE, '>', '+', '~'])) {
                $identifier .= $char;
            }
        }

        $this->identifiers[] = $identifier;
        $this->identifiers = array_values(
            array_filter($this->identifiers, function ($identifier) {
                return !in_array($identifier, [self::EMPTY_STRING, self::SPACE, self::BRACKETS]);
            })
        );
    }

    private function isDelimiter($char)
    {
        return in_array($char, explode('|', self::IDENTIFIER_DELIMITERS));
    }
}
