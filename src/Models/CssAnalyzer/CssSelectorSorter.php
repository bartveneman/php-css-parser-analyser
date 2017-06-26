<?php

namespace Wallace\Models\CssAnalyzer;

use Wallace\Models\CssParser\CssSpecificity;

class CssSelectorSorter
{

    /**
     * Sorts high to low
     */
    public static function sortBySpecificity(array $selectors)
    {
        $sorted = $sel_a = $sel_b = $sel_c = [];

        for ($i = 0; $i < count($selectors); $i += 1) {
            $specificity = $selectors[$i]->getSpecificity();
            $sel_a[] = $specificity[CssSpecificity::SPECIFICITY_IDS];
            $sel_b[] = $specificity[CssSpecificity::SPECIFICITY_CLASSES_ATTRIBUTES];
            $sel_c[] = $specificity[CssSpecificity::SPECIFICITY_ELEMENTS];
        }

        array_multisort($sel_a, SORT_DESC, $sel_b, SORT_DESC, $sel_c, SORT_DESC);

        for ($i = 0; $i < count($selectors); $i += 1) {
            $sorted[$i] = [
            CssSpecificity::SPECIFICITY_IDS => $sel_a[$i],
            CssSpecificity::SPECIFICITY_CLASSES_ATTRIBUTES => $sel_b[$i],
            CssSpecificity::SPECIFICITY_ELEMENTS => $sel_c[$i],
            ];
        }

        return $sorted;
    }

    /**
     * Sorts high to low
     */
    public static function sortByIdentifierCount(array $selectors)
    {
        usort($selectors, function ($a, $b) {
            if ($a->getTotalIdentifiers() === $b->getTotalIdentifiers()) {
                return 0;
            }

            return $a->getTotalIdentifiers() > $b->getTotalIdentifiers() ? -1 : 1;
        });

        return $selectors;
    }
}
