<?php

namespace WPMultiLanguage;

class WPMultiLanguage
{
    public function __construct()
    {
        add_action('init', [$this, 'init']);
        add_action('get_available_languages', [$this, 'get_available_languages'], 10, 2);
    }

    public function init()
    {
        global $wp_multi_language;
        $lang = null;

        if (isset($_REQUEST['lang']) && in_array($_REQUEST['lang'], $wp_multi_language['langs'])) {
            $lang = $_REQUEST['lang'];
        }
        if (
            !$lang
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
}
