<?php

namespace Wallace\Models\CssAnalyzer;

class CssKeyframesAnalyzer implements CssAnalyzerInterface
{
    private $keyframes;

    public function __construct(array $keyframes)
    {
        $this->keyframes = $keyframes;
    }

    public function getTotalKeyframes()
    {
        return count($this->keyframes);
    }

    public function getTotalPrefixedKeyframes()
    {
        $prefixed = array_filter($this->keyframes, function ($keyframe) {
            return $keyframe->isPrefixed();
        });

        return count($prefixed);
    }

    public function getUniqueKeyframes()
    {
        return array_values(
            array_unique(
                array_map(
                    function ($keyframe) {
                        return $keyframe->getIdentifier();
                    },
                    $this->keyframes
                )
            )
        );
    }

    public function getTotalUniqueKeyframes()
    {
        return count($this->getUniqueKeyframes());
    }

    public function analyze()
    {
        return [
            'total_keyframes' => $this->getTotalKeyframes(),
            'total_prefixed_keyframes' => $this->getTotalPrefixedKeyframes(),
            'unique_keyframes' => $this->getUniqueKeyframes(),
            'total_unique_keyframes' => $this->getTotalUniqueKeyframes()
        ];
    }
}
