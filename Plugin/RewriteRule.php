<?php

namespace WPMultiLanguage\Plugin;

class RewriteRule
{
    public function __construct()
    {
        add_filter('generate_rewrite_rules', [$this, 'filterGenerateRewriteRules'], PHP_INT_MAX);

        add_filter('redirect_canonical', [$this, 'filterRedirectCanonical'], PHP_INT_MAX, 2);

        add_filter('page_link', [$this, 'filterPageLink'], PHP_INT_MAX, 3);
        add_filter('post_link', [$this, 'filterPostLink'], PHP_INT_MAX, 3);
        add_filter('term_link', [$this, 'filterTermLink'], PHP_INT_MAX, 3);
    }

    public function filterGenerateRewriteRules($wp_rewrite)
    {
        global $wp_multi_language;
        $langs = implode('|', array_diff($wp_multi_language['langs'], [$wp_multi_language['default_lang']]));
        $rules = array();

        $rules["^({$langs})/?$"] = 'index.php?page_id=' . get_option( 'page_on_front' ) . '&lang=$matches[1]';

        foreach ($wp_rewrite->rules as $regexp => $urlStructure) {
            $regexpOrig = $regexp;
            $urlStructureOrig = $urlStructure;

            if (
                false !== strpos($urlStructure, 'pagename=$matches')
                || false !== strpos($urlStructure, 'product=$matches')
                || false !== strpos($urlStructure, 'category_name=$matches')
                || false !== strpos($urlStructure, 'product_cat=$matches')
            ) {
                for ($i = 10; $i > 0; $i--) {
                    $urlStructure = str_replace('$matches[' . $i . ']', '$matches[' . ($i+1) . ']', $urlStructure);
                }
                $urlStructure .= '&lang=$matches[1]';

                $regexp = "({$langs})/" . $regexp;

                $rules[$regexp] = $urlStructure;
            }

            $rules[$regexpOrig] = $urlStructureOrig;
        }

        $wp_rewrite->rules = $rules;
    }

    public function filterRedirectCanonical($redirect_url, $requested_url )
    {
        $originRedirectUrl = $redirect_url;
        $home = home_url();

        $redirect_url = trim(str_replace($home, '', $redirect_url), '/');
        $requested_url =  trim(str_replace($home, '', $requested_url), '/');
        if ($redirect_url == substr($requested_url, 3)) {
            return null;
        }
        return $originRedirectUrl;
    }

    public function filterPageLink($link, $postId, $sample)
    {
        global $wp_multi_language;
        if (WP_MULTI_LANGUAGE_LANG == $wp_multi_language['default_lang']) {
            return $link;
        }
        return wp_multi_language_url_override($link, WP_MULTI_LANGUAGE_LANG);
    }

    public function filterPostLink($link, $post, $leavename)
    {
        global $wp_multi_language;

        if (!isset($wp_multi_language['entity'][$post->post_type])) {
            return $link;
        }
        if (WP_MULTI_LANGUAGE_LANG == $wp_multi_language['default_lang']) {
            return $link;
        }

        return wp_multi_language_url_override($link, WP_MULTI_LANGUAGE_LANG);
    }

    public function filterTermLink($termlink, $term, $taxonomy)
    {
        global $wp_multi_language;

        if (WP_MULTI_LANGUAGE_LANG == $wp_multi_language['default_lang']) {
            return $termlink;
        }

        return wp_multi_language_url_override($termlink, WP_MULTI_LANGUAGE_LANG);
    }
}
