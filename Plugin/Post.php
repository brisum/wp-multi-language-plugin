<?php

namespace WPMultiLanguage\Plugin;

use WPMultiLanguage\TranslationService;

class Post
{
    protected $translations = [];

    public function __construct()
    {
        add_filter('wp_insert_post_data', [$this, 'filterInsertPostData'], PHP_INT_MIN, 2);
        add_filter('pre_post_update', [$this, 'actionPrePostUpdate'], PHP_INT_MAX, 2);
        add_filter('wp_multi_language_translate_post', [$this, 'filterTranslatePost'], 10, 1);
        add_filter('posts_results', [$this, 'filterPostsResults'], 10, 2);
    }

    public function filterInsertPostData($data, $postarr)
    {
        global $wp_multi_language;

        if (WP_MULTI_LANGUAGE_LANG == $wp_multi_language['default_lang']) {
            return $data;
        }
        if (!isset($postarr['ID'])) {
            return $data;
        }
        if (!isset($data['post_type'])) {
            return $data;
        }
        if (!isset($wp_multi_language['entity'][$data['post_type']])) {
            return $data;
        }

        $postId = $postarr['ID'];
        $rawPost = $postId ? $this->getRawPost($postId) : null;

        $this->translations[$postId] = [];
        foreach ($wp_multi_language['entity'][$data['post_type']] as $field) {
            if (isset($data[$field])) {
                $this->translations[$postId][WP_MULTI_LANGUAGE_LANG][$field] = $data[$field];

                if ($rawPost && isset($rawPost[$field])) {
                    $data[$field] = $rawPost[$field];
                } else {
                    unset($data[$field]);
                }
            }
        }

        return $data;
    }

    public function actionPrePostUpdate($postId, $data)
    {
        if (empty($this->translations[$postId])) {
            return;
        }

        $translationService = new TranslationService();
        foreach ($this->translations[$postId] as $lang => $fields) {
            foreach ($fields as $field => $value) {
                $translationService->update('posts', $postId, $lang, $field, $value);
            }
        }
    }

    public function filterTranslatePost($post)
    {
        global $wp_multi_language;

        if (WP_MULTI_LANGUAGE_LANG == $wp_multi_language['default_lang']) {
            return $post;
        }

        $translationService = new TranslationService();
        $postId = is_array($post) ? $post['ID'] : $post->ID;
        $translations = $translationService->getTranslationsEntity('posts', $postId, WP_MULTI_LANGUAGE_LANG);

        if (is_array($post)) {
            foreach ($translations as $field => $value) {
                if (isset($post[$field])) {
                    $post[$field] = $value;
                }
            }
        } else {
            foreach ($translations as $field => $value) {
                if (isset($post->$field)) {
                    $post->$field = $value;
                }
            }
        }
        return $post;
    }

    public function filterPostsResults($posts, $wp_query)
    {
        global $wp_multi_language;

        if (WP_MULTI_LANGUAGE_LANG == $wp_multi_language['default_lang']) {
            return $posts;
        }

        $translationService = new TranslationService();
        $postIds = [];

        foreach ($posts as $post) {
            $postIds[] = $post->ID;
        }
        $translationsEntities = $translationService->getTranslationsEntities('posts', $postIds, WP_MULTI_LANGUAGE_LANG);

        foreach ($posts as $post) {
            foreach ($translationsEntities[$post->ID] as $field => $value) {
                if (isset($post->$field)) {
                    $post->$field = $value;
                }
            }
        }

        return $posts;
    }

    /**
     * @param $postId
     * @return array|null
     */
    protected function getRawPost($postId)
    {
        global $wpdb;
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->posts WHERE ID = %d LIMIT 1", $postId), ARRAY_A);
    }
}

