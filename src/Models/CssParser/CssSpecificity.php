<?php

namespace Wallace\Models\CssParser;

class CssSpecificity
{

    // The following regular expressions assume that selectors matching the
    // preceding regular expressions have been removed
    const ATTRIBUTE_REGEX = '/(\[[^\]]+\])/';
    const ID_REGEX = '/(#[^\s\+>~\.\[:]+)/';
    const CLASS_REGEX = '/(\.[^\s\+>~\.\[:]+)/';
    const PSEUDO_ELEMENT_REGEX = '/(::[^\s\+>~\.\[:]+|:first-line|:first-letter|:before|:after)/i';

    // A regex for pseudo classes with brackets
    // - :nth-child(), :nth-last-child(), :nth-of-type(), :nth-last-type(), :lang()
    const PSEUDO_CLASS_BRACKETS_REGEX = '/(:[\w-]+\([^\)]*\))/i';

    // A regex for other pseudo classes, which don't have brackets
    const PSEUDO_CLASS_REGEX = '/(:[^\s\+>~\.\[:]+)/';
    const ELEMENT_REGEX = '/([^\s\+>~\.\[:]+)/';
    const NOT_REGEX = '/:not\(([^\)]*)\)/';
    const UNIVERSAL_AND_SEPARATOR_CHARACTERS_REGEX = '/[\*\s\+>~]/';

    const SPECIFICITY_IDS = 'a';
    const SPECIFICITY_CLASSES_ATTRIBUTES = 'b';
    const SPECIFICITY_ELEMENTS = 'c';

    private $selector;
    private $value = [
        self::SPECIFICITY_IDS => 0,
        self::SPECIFICITY_CLASSES_ATTRIBUTES => 0,
        self::SPECIFICITY_ELEMENTS => 0
    ];

    public function __construct($selector)
    {
        $this->selector = $selector;
        $this->setSpecificity($selector);
    }

    public function getSpecificity()
    {
        return $this->value;
    }

    private function setSpecificity($selector)
    {
        // Remove the negation psuedo-class (:not) but leave its argument because
        // specificity is calculated on its argument
        if (preg_match(self::NOT_REGEX, $selector) !== false) {
            $this->selector = preg_replace(self::NOT_REGEX, '     $1 ', $selector);
        }

        // Add attribute selectors to parts collection (type b)
        $this->findMatch(self::ATTRIBUTE_REGEX, self::SPECIFICITY_CLASSES_ATTRIBUTES);

        // Add ID selectors to parts collection (type a)
        $this->findMatch(self::ID_REGEX, self::SPECIFICITY_IDS);

        // Add class selectors to parts collection (type b)
        $this->findMatch(self::CLASS_REGEX, self::SPECIFICITY_CLASSES_ATTRIBUTES);

        // Add pseudo-element selectors to parts collection (type c)
        $this->findMatch(self::PSEUDO_ELEMENT_REGEX, self::SPECIFICITY_ELEMENTS);

        // Add pseudo-class selectors to parts collection (type b)
        $this->findMatch(self::PSEUDO_CLASS_BRACKETS_REGEX, self::SPECIFICITY_CLASSES_ATTRIBUTES);
        $this->findMatch(self::PSEUDO_CLASS_REGEX, self::SPECIFICITY_CLASSES_ATTRIBUTES);

        // Remove universal selector and separator characters
        $this->selector = preg_replace(
            self::UNIVERSAL_AND_SEPARATOR_CHARACTERS_REGEX,
            ' ',
            $this->selector
        );

        // The only things left should be element selectors (type c)
        $this->findMatch(self::ELEMENT_REGEX, self::SPECIFICITY_ELEMENTS);
    }

    // Find matches for a regular expression in a string and push their details to parts
    //
    // Type is "a" for IDs, "b" for classes, attributes and pseudo-classes and "c"
    // for elements and pseudo-elements
    private function findMatch($regex, $type)
    {
        if (preg_match($regex, $this->selector) === false) {
            return;
        }

        preg_match_all($regex, $this->selector, $matches);
        foreach ($matches[0] as $match) {
            $this->value[$type] += 1;
            $index = strpos($this->selector, $match);
            $length = strlen($match);

            // Replace this simple selector with whitespace so it won't be counted
            // in further simple selectors
            $this->selector = substr_replace(
                $this->selector,
                str_pad(' ', $length),
                $index,
                $length
            );
        }
    }
}
