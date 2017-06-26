<?php

namespace Wallace\Models\CssParser;

class CssRule
{

    const DECLARATION_BLOCK_REGEX = '/\{(.+)\}/';
    const SELECTOR_BLOCK_REGEX = '/([^{]+)\{/';
    const DECLARATION_DELIMITER = ';';
    const SELECTOR_DELIMITER = ',';
    const EMPTY_STRING = '';

    private $selectors;
    private $declarations;
    private $is_empty;

    public function __construct($raw)
    {
        $this->raw = $raw;
        $this->selectors = $this->getSelectors();
        $declarations = $this->getDeclarations();
        $this->declarations = $declarations;
        $this->setEmpty($declarations);
    }

  /**
   * https://github.com/katiefenn/parker/blob/master/lib/CssRule.js#L11
   */
    public function getSelectors()
    {
        return $this->getSelectorsRaw(
            $this->getSelectorBlock($this->raw)
        );
    }

  /**
   * https://github.com/katiefenn/parker/blob/master/lib/CssRule.js#L15
   */
    public function getDeclarations()
    {
        return $this->getDeclarationsRaw(
            $this->getDeclarationBlock($this->raw)
        );
    }

  /**
   * https://github.com/katiefenn/parker/blob/master/lib/CssRule.js#L19
   */
    private function getSelectorBlock($rule)
    {
        preg_match_all(self::SELECTOR_BLOCK_REGEX, $rule, $results);

        return $results[1][0];
    }

  /**
   * https://github.com/katiefenn/parker/blob/master/lib/CssRule.js#L26
   */
    private function getSelectorsRaw($selector_block)
    {
        $untrimmed_selectors = explode(self::SELECTOR_DELIMITER, $selector_block);

        $trimmed_selectors = array_map(function ($untrimmed_selector) {
            return trim($untrimmed_selector);
        }, $untrimmed_selectors);

        $selectors = array_filter($trimmed_selectors, function ($trimmed_selector) {
            return $trimmed_selector !== false;
        });

        return $selectors;
    }

  /**
   * https://github.com/katiefenn/parker/blob/master/lib/CssRule.js#L35
   */
    private function getDeclarationBlock($rule)
    {
        preg_match_all(self::DECLARATION_BLOCK_REGEX, $rule, $results);

        if ($results === 0 || !$results[0]) {
            return '';
        }

        return $results[1][0];
    }

  /**
   * https://github.com/katiefenn/parker/blob/master/lib/CssRule.js#L46
   */
    private function getDeclarationsRaw($declaration_block)
    {
        $declaration_block = explode(
            self::DECLARATION_DELIMITER,
            trim($declaration_block)
        );

        // lodash .compact: return array with all falsy values removed
        $untrimmed_declarations = array_filter($declaration_block, function ($declaration_block) {
            return $declaration_block !== self::EMPTY_STRING;
        });

        $trimmed_declarations = array_map(function ($declaration_block) {
            return trim($declaration_block);
        }, $untrimmed_declarations);

        return $trimmed_declarations;
    }

    private function setEmpty()
    {
        $this->is_empty = count($this->getDeclarations()) === 0;
    }

    public function isEmpty()
    {
        return $this->is_empty;
    }
}
