<?php

namespace WPMultiLanguage\Plugin;

use WPMultiLanguage\TranslationService;

class Term
{
    protected $translations = [];

    public function __construct()
    {
        add_filter('wp_update_term_data', [$this, 'filterUpdateTermData'], PHP_INT_MIN, 4);
        add_action('edited_terms', [$this, 'actionEditedTerm'], 10, 2);

        add_filter('wp_multi_language_translate_term', [$this, 'filterTranslateTerm'], 10, 1);
        add_filter('wp_multi_language_translate_terms', [$this, 'filterTranslateTerms'], 10, 1);
    }

    public function filterUpdateTermData($data, $termId, $taxonomy, $args)
    {
        global $wp_multi_language;

        if (WP_MULTI_LANGUAGE_LANG == $wp_multi_language['default_lang']) {
            return $data;
        }

        $rawTerm = $this->getRawTerm($termId);

        $this->translations[$termId] = [];
        foreach ($wp_multi_language['entity']['term'] as $field) {
            if (isset($data[$field])) {
                $this->translations[$termId][WP_MULTI_LANGUAGE_LANG][$field] = $data[$field];

                if ($rawTerm && isset($rawTerm[$field])) {
                    $data[$field] = $rawTerm[$field];
                } else {
                    unset($data[$field]);
                }
            }
        }

        return $data;
    }

    public function actionEditedTerm($termId, $taxonomy)
    {
        if (empty($this->translations[$termId])) {
            return;
        }

        $translationService = new TranslationService();
        foreach ($this->translations[$termId] as $lang => $fields) {
            foreach ($fields as $field => $value) {
                $translationService->update('terms', $termId, $lang, $field, $value);
            }
        }
    }

    public function filterTranslateTerm($term)
    {
        global $wp_multi_language;

        if (WP_MULTI_LANGUAGE_LANG == $wp_multi_language['default_lang']) {
            return $term;
        }

        $translationService = new TranslationService();
        $termId = is_array($term) ? $term['term_id'] : $term->term_id;
        $translations = $translationService->getTranslationsEntity('terms', $termId, WP_MULTI_LANGUAGE_LANG);

        if (is_array($term)) {
            foreach ($translations as $field => $value) {
                if (isset($term[$field])) {
                    $term[$field] = wp_unslash($value);
                }
            }
        } else {
            foreach ($translations as $field => $value) {
                if (isset($term->$field)) {
                    $term->$field = wp_unslash($value);
                }
            }
        }

        return $term;
    }

    public function filterTranslateTerms($terms, $wp_query = null)
    {
        global $wp_multi_language;

        if (WP_MULTI_LANGUAGE_LANG == $wp_multi_language['default_lang']) {
            return $terms;
        }

        $translationService = new TranslationService();
        $termIds = [];

        foreach ($terms as $term) {
            $termIds[] = $term->term_id;
        }
        $translationsEntities = $translationService->getTranslationsEntities('terms', $termIds, WP_MULTI_LANGUAGE_LANG);

        foreach ($terms as $term) {
            foreach ($translationsEntities[$term->term_id] as $field => $value) {
                if (isset($term->$field)) {
                    $term->$field = wp_unslash($value);
                }
            }
        }

        return $terms;
    }

    /**
     * @param $postId
     * @return array|null
     */
    protected function getRawTerm($termId)
    {
        global $wpdb;
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->terms WHERE term_id = %d LIMIT 1", $termId), ARRAY_A);
    }
}

