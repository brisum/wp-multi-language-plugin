<?php

namespace WPMultiLanguage\Plugin;

use WPMultiLanguage\TranslationService;

class TermTaxonomy
{
    protected $translations = [];

    public function __construct()
    {
        add_filter('wp_update_term_taxonomy_data', [$this, 'filterUpdateTermTaxonomyData'], PHP_INT_MIN, 4);
        add_action('edited_term_taxonomy', [$this, 'actionEditedTermTaxonomy'], 10, 2);

        add_filter('wp_multi_language_translate_term_taxonomy', [$this, 'filterTranslateTermTaxonomy'], 10, 1);
        add_filter('wp_multi_language_translate_term_taxonomies', [$this, 'filterTranslateTermTaxonomies'], 10, 1);
    }

    public function filterUpdateTermTaxonomyData($data, $termTaxonomyId)
    {
        global $wp_multi_language;

        if (WP_MULTI_LANGUAGE_LANG == $wp_multi_language['default_lang']) {
            return $data;
        }

        $rawTermTaxonomy = $this->getRawTermTaxonomy($termTaxonomyId);

        $this->translations[$termTaxonomyId] = [];
        foreach ($wp_multi_language['entity']['term_taxonomy'] as $field) {
            if (isset($args[$field])) {
                $this->translations[$termTaxonomyId][WP_MULTI_LANGUAGE_LANG][$field] = $data[$field];

                if ($rawTermTaxonomy && isset($rawTermTaxonomy[$field])) {
                    $data[$field] = $rawTermTaxonomy[$field];
                } else {
                    unset($data[$field]);
                }
            }
        }

        return $data;
    }

    public function actionEditedTermTaxonomy($termTaxonomyId, $taxonomy)
    {
        if (empty($this->translations[$termTaxonomyId])) {
            return;
        }

        $translationService = new TranslationService();
        foreach ($this->translations[$termTaxonomyId] as $lang => $fields) {
            foreach ($fields as $field => $value) {
                $translationService->update('term_taxonomy', $termTaxonomyId, $lang, $field, $value);
            }
        }
    }

    public function filterTranslateTermTaxonomy($term)
    {
        global $wp_multi_language;

        if (WP_MULTI_LANGUAGE_LANG == $wp_multi_language['default_lang']) {
            return $term;
        }

        $translationService = new TranslationService();
        $termTaxonomyId = is_array($term) ? $term['term_taxonomy_id'] : $term->term_taxonomy_id;
        $translations = $translationService->getTranslationsEntity('term_taxonomy', $termTaxonomyId, WP_MULTI_LANGUAGE_LANG);

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

    public function filterTranslateTermTaxonomies($terms, $wp_query = null)
    {
        global $wp_multi_language;

        if (WP_MULTI_LANGUAGE_LANG == $wp_multi_language['default_lang']) {
            return $terms;
        }

        $translationService = new TranslationService();
        $termTaxonomyIds = [];

        foreach ($terms as $term) {
            $termTaxonomyIds[] = $term->term_taxonomy_id;
        }
        $translationsEntities = $translationService->getTranslationsEntities('term_taxonomy', $termTaxonomyIds, WP_MULTI_LANGUAGE_LANG);

        foreach ($terms as $term) {
            foreach ($translationsEntities[$term->term_taxonomy_id] as $field => $value) {
                if (isset($term->$field)) {
                    $term->$field = wp_unslash($value);
                }
            }
        }

        return $terms;
    }

    /**
     * @param $termTaxonomyId
     * @return array|null
     */
    protected function getRawTermTaxonomy($termTaxonomyId)
    {
        global $wpdb;
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * 
            FROM $wpdb->term_taxonomy 
            WHERE term_taxonomy_id = %d LIMIT 1",
            $termTaxonomyId
        ), ARRAY_A);
    }
}

