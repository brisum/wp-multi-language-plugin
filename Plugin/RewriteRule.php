<?php

namespace WPMultiLanguage\Plugin;

class RewriteRule
{
    public function __construct()
    {
        add_filter('generate_rewrite_rules', [$this, 'filterGenerateRewriteRules']);
        add_filter('redirect_canonical', [$this, 'filterRedirectCanonical'], 10, 2);
    }

    public function filterGenerateRewriteRules($wp_rewrite)
    {
        global $wp_multi_language;
        $rules = array();

        foreach ($wp_multi_language['langs'] as $lang) {
            if ($wp_multi_language['default_lang'] == $lang) {
                continue;
            }

            $rules["^{$lang}/?$"] = 'index.php?page_id=' . get_option( 'page_on_front' ) . "&lang={$lang}";
        }


        // merge with global rules
        $wp_rewrite->rules = $rules + $wp_rewrite->rules;
    }

    public function filterRedirectCanonical($redirect_url, $requested_url )
    {
        $originRedirectUrl = $redirect_url;
        $home = home_url();

        $redirect_url = str_replace($home, '', $redirect_url);
        $requested_url = str_replace($home, '', $requested_url);
        if ($redirect_url == substr($requested_url, 3)) {
            return null;
        }
        return $originRedirectUrl;
    }
}
