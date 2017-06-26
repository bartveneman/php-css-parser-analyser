<?php

namespace Wallace\Models\CssAnalyzer;

use Wallace\Models\CssParser\CssSelector;
use Wallace\Models\CssParser\CssSpecificity;

class CssSelectorAnalyzer implements CssAnalyzerInterface
{
    const ID_REGEX = '/(?![^[]*])\#/i';
    const JS_REGEX = '/[\.|\#|(?:=\"|\')]js/i';
    const UNIVERSAL_REGEX = '/(?![^[]*])\*/i';

    private $selectors;
    private $unique_selectors;
    private $id_selectors;
    private $js_selectors;
    private $universal_selectors;
    private $max_specificity_selectors;
    private $max_identifier_selectors;
    private $browser_hacks;

    public function __construct(array $selectors)
    {
        $this->selectors = $selectors;
        $this->setIdSelectors($selectors);
        $this->setJsSelectors($selectors);
        $this->setUniversalSelectors($selectors);
        $this->setMaxSpecificitySelectors($selectors);
        $this->setMaxIdentifierSelectors($selectors);
        $this->setBrowserHacks($selectors);
    }

    public function getTotalSelectors()
    {
        return count($this->selectors);
    }

    public function getTotalUniqueSelectors()
    {
        $selectors = [];

        foreach ($this->selectors as $selector) {
            $selectors[] = $selector->getSelector();
        }

        return count(array_values(array_unique($selectors)));
    }

    private function setIdSelectors(array $selectors)
    {
        $id_selectors = [];

        foreach ($selectors as $selector) {
            $selector = $selector->getSelector();

            // Only add ID-selectors to the list
            if (preg_match(self::ID_REGEX, $selector)) {
                $id_selectors[] = $selector;
            }
        }

        $this->id_selectors = $id_selectors;
        return $this;
    }

    public function getIdSelectors()
    {
        return array_values(
            array_unique($this->id_selectors)
        );
    }

    public function getTotalIdSelectors()
    {
        return count($this->id_selectors);
    }

    private function setUniversalSelectors(array $selectors)
    {
        $universals = [];

        foreach ($selectors as $selector) {
            $selector = $selector->getSelector();

            // Only add Universal selectors to the list
            if (preg_match(self::UNIVERSAL_REGEX, $selector)) {
                $universals[] = $selector;
            }
        }

        $this->universal_selectors = $universals;
        return $this;
    }

    public function getUniversalSelectors()
    {
        return array_values(
            array_unique($this->universal_selectors)
        );
    }

    public function getTotalUniversalSelectors()
    {
        return count($this->universal_selectors);
    }

    private function setJsSelectors(array $selectors)
    {
        $js_selectors = [];

        foreach ($this->selectors as $selector) {
            $selector = $selector->getSelector();

            // Only add JS-selectors to the list
            if (preg_match(self::JS_REGEX, $selector)) {
                $js_selectors[] = $selector;
            }
        }

        $this->js_selectors = $js_selectors;
        return $this;
    }

    public function getJsSelectors()
    {
        return array_values(
            array_unique($this->js_selectors)
        );
    }

    public function getTotalJsSelectors()
    {
        return count($this->js_selectors);
    }

    public function setMaxSpecificitySelectors(array $selectors)
    {
        $top_selectors = [];

        // Sort selectors by specificity (highest first)
        $sorted = CssSelectorSorter::sortBySpecificity($selectors);
        $top = current($sorted);

        for ($i = 0; $i < count($sorted); $i += 1) {
            $specificity = $selectors[$i]->getSpecificity();

            if ($specificity === $top) {
                $top_selectors[] = $selectors[$i]->getSelector();
            }
        }

        $this->max_specificity_selectors = array_values(array_unique($top_selectors));
        return $this;
    }

    public function getMaxSpecificitySelectors()
    {
        return $this->max_specificity_selectors;
    }

    public function getMaxSpecificity()
    {
        $top_specificity_selector = current($this->max_specificity_selectors);
        $selector = new CssSelector($top_specificity_selector);

        return $selector->getSpecificity();
    }

    private function setMaxIdentifierSelectors(array $selectors)
    {
        $max_identifier_selectors = [];
        $sorted = CssSelectorSorter::sortByIdentifierCount($selectors);
        $top = $next = current($sorted);

        // Find all selectors with the same amount of identifiers as the selector
        // with the most identifiers
        while ($next && $next->getTotalIdentifiers() === $top->getTotalIdentifiers()) {
            $max_identifier_selectors[] = array_shift($sorted);
            $next = current($sorted);
        }

        $this->max_identifier_selectors = $max_identifier_selectors;
        return $this;
    }

    public function getMaxIdentifierSelectors()
    {
        return array_values(
            array_unique(
                array_map(function ($selector) {
                    return $selector->getSelector();
                }, $this->max_identifier_selectors)
            )
        );
    }

    public function getMaxIdentifiers()
    {
        $selector = current($this->max_identifier_selectors);

        return $selector->getTotalIdentifiers();
    }

    private function getTotalIdentifiers()
    {
        $total = 0;

        foreach ($this->selectors as $selector) {
            $total += $selector->getTotalIdentifiers();
        }

        return $total;
    }

    public function getAverageIdentifiers()
    {
        $identifiers = $this->getTotalIdentifiers();
        $selectors = count($this->selectors);

        // Don't divide by zero
        if ($selectors === 0 || $identifiers === 0) {
            return 0;
        }

        return $identifiers / $selectors;
    }

    private function setBrowserHacks($selectors)
    {
        $browser_hacks = [];

        foreach ($selectors as $selector) {
            if ($selector->isBrowserHack()) {
                $browser_hacks[] = $selector->getSelector();
            }
        }

        $this->browser_hacks = $browser_hacks;
    }

    public function getBrowserHacks()
    {
        return array_values(
            array_unique(
                $this->browser_hacks
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
            'total_selectors' => $this->getTotalSelectors(),
            'total_unique_selectors' => $this->getTotalUniqueSelectors(),
            'total_js_selectors' => $this->getTotalJsSelectors(),
            'total_id_selectors' => $this->getTotalIdSelectors(),
            'total_universal_selectors' => $this->getTotalUniversalSelectors(),
            'max_specificity' => $this->getMaxSpecificity(),
            'max_specificity_selectors' => $this->getMaxSpecificitySelectors(),
            'max_identifiers' => $this->getMaxIdentifiers(),
            'average_identifiers' => $this->getAverageIdentifiers(),
            'max_identifier_selectors' => $this->getMaxIdentifierSelectors(),
            'browser_selector_hacks' => $this->getBrowserHacks(),
            'total_browser_selector_hacks' => $this->getTotalBrowserHacks()
        ];
    }
}
