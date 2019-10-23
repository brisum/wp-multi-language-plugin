<?php

namespace WPMultiLanguage;

class WPMultiLanguage
{
    public function __construct()
    {
        $GLOBALS['request_id'] = mt_rand(100, 1000);

        add_action('init', [$this, 'init'], PHP_INT_MIN);
        add_action('admin_init', [$this, 'init'], PHP_INT_MIN);
        add_action('after_setup_theme', [$this, 'init'], PHP_INT_MIN);

        add_action('get_available_languages', [$this, 'get_available_languages'], 10, 2);
        add_filter( 'query_vars', [$this, 'filterQueryVars']);
        add_filter('page_row_actions', [$this, 'filterPostRowActions'], 10, 1);

        add_filter('post_row_actions', [$this, 'filterPostRowActions'], 10, 1);
        add_filter('get_edit_post_link', [$this, 'filterEditPostLink'], 10, 2);
        add_filter('edit_post_link', [$this, 'filterEditPostLink'], 10, 2);
    }

    public function init()
    {
        if (defined('WP_MULTI_LANGUAGE_LANG')) {
            return;
        }

        global $wp_multi_language;
        $lang = null;

        if (isset($_REQUEST['lang']) && in_array($_REQUEST['lang'], $wp_multi_language['langs'])) {
            $lang = $_REQUEST['lang'];
        }
        if (isset($_SERVER['REQUEST_URI']) && '/ua/' == substr($_SERVER['REQUEST_URI'], 0, 4)) {
            $lang = 'ua';
        }
        if (
            !$lang
            && 'post' == strtolower($_SERVER['REQUEST_METHOD'])
            && isset($_SERVER['HTTP_REFERER'])
            && preg_match('/lang=([a-z]+)/', $_SERVER['HTTP_REFERER'], $match)
        ) {
            $lang = in_array($match[1], $wp_multi_language['langs']) ? $match[1] : null;
        }
        if (
            !$lang
            && defined('REST_REQUEST')
            && REST_REQUEST
            && isset($_SERVER['HTTP_REFERER'])
            && preg_match('/lang=([a-z]+)/', $_SERVER['HTTP_REFERER'], $match)
        ) {
            $lang = in_array($match[1], $wp_multi_language['langs']) ? $match[1] : null;
        }
        if (!$lang) {
            $lang = $wp_multi_language['default_lang'];
        }

        define('WP_MULTI_LANGUAGE_LANG', $lang);
        switch (WP_MULTI_LANGUAGE_LANG) {
            case 'ua':
                switch_to_locale('uk');
                break;
        }
    }

    public function get_available_languages($languages, $dir)
    {
        $languages = ['uk'];
        return $languages;
    }

    public function filterQueryVars($vars) {
        $vars[] = 'lang';
        return $vars;
    }

    public function filterPostRowActions($actions) {
        unset($actions['inline']);
        unset($actions['inline hide-if-no-js']);

        return $actions;
    }

    public function filterEditPostLink($link, $postId)
    {
        $urlParts = parse_url($link);

        if (empty($urlParts['query'])) {
            $urlParts['query'] = "lang=" . WP_MULTI_LANGUAGE_LANG;
        } else {
            $urlParts['query'] .= "&lang=" . WP_MULTI_LANGUAGE_LANG;
        }

        return build_url($urlParts);
    }
}
