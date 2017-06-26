<?php

namespace Wallace\Models\CssParser;

class CssParser
{
    private $rules = [];
    private $media_queries = [];
    private $selectors = [];
    private $declarations = [];
    private $keyframes = [];
    private $imports = [];
    private $supports_rules = [];

    public function __construct($raw)
    {
        $stylesheet = new CssStylesheet($raw);
        $raw_rules = $stylesheet->getRules();

        $this->media_queries = array_map(function ($raw_query) {
            return new CssMediaQuery($raw_query);
        }, $stylesheet->getMediaQueries());

        $this->keyframes = array_map(function ($raw_keyframe) {
            return new CssKeyframe($raw_keyframe);
        }, $stylesheet->getKeyframes());

        $this->imports = array_map(function ($raw_import) {
            return new CssImport($raw_import);
        }, $stylesheet->getImports());

        $this->supports_rules = array_map(function ($raw_supports_rule) {
            return new CssSupportsRule($raw_supports_rule);
        }, $stylesheet->getSupportsRules());

        foreach ($this->media_queries as $media_query) {
            foreach ($media_query->getRules() as $mq_rule) {
                $raw_rules[] = $mq_rule;
            }
        }

        foreach ($this->keyframes as $keyframe) {
            foreach ($keyframe->getRules() as $rule) {
                $rule = new CssRule($rule);
                foreach ($rule->getDeclarations() as $declaration) {
                    $this->declarations[] = new CssDeclaration($declaration);
                }
            }
        }

        foreach ($this->supports_rules as $support_rule) {
            foreach ($support_rule->getRules() as $rule) {
                $rule = new CssRule($rule);
                foreach ($rule->getDeclarations() as $declaration) {
                    $this->declarations[] = new CssDeclaration($declaration);
                }
            }
        }

        foreach ($raw_rules as $raw_rule) {
            $rule = new CssRule($raw_rule);
            $this->rules[] = $rule;

            foreach ($rule->getSelectors() as $raw_selector) {
                $selector = new CssSelector($raw_selector);
                $this->selectors[] = $selector;
            }

            foreach ($rule->getDeclarations() as $raw_declaration) {
                $this->declarations[] = new CssDeclaration($raw_declaration);
            }
        }
    }

    public function getRules()
    {
        return $this->rules;
    }

    public function getMediaQueries()
    {
        return $this->media_queries;
    }

    public function getKeyframes()
    {
        return $this->keyframes;
    }

    public function getSelectors()
    {
        return $this->selectors;
    }

    public function getDeclarations()
    {
        return $this->declarations;
    }

    public function getImports()
    {
        return $this->imports;
    }

    public function getSupportsRules()
    {
        return $this->supports_rules;
    }
}
