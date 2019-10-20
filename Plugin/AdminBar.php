<?php

namespace WPMultiLanguage\Plugin;

class AdminBar
{
    public function __construct()
    {
        add_action('admin_bar_menu', [$this, 'actionAdminBarMenu'], PHP_INT_MAX);
    }

    function actionAdminBarMenu($admin_bar)
    {
        global $wp, $wp_multi_language;
        $url = $wp->request ? $wp->request : $_SERVER['REQUEST_URI'];
        $urlParts = parse_url($url);

        $switcherLanguages = [];
        foreach ($wp_multi_language['langs'] as $lang) {
            if (WP_MULTI_LANGUAGE_LANG == $lang) {
                continue;
            }

            $urlParts['query'] = $_GET;
            $urlParts['query']['lang'] = $lang;
            $urlParts['query'] = http_build_query($urlParts['query']);
            $switcherLanguages[] = sprintf(
                '<li data-href="%s"><img src="' . WP_MULTI_LANGUAGE_URL . 'assets/img/flags/%s.png" alt=""></li>',
                build_url($urlParts),
                $lang
            );
        }
        $switcherLanguages = implode('', $switcherLanguages);
        $switcher = '
            <img src="' . WP_MULTI_LANGUAGE_URL . 'assets/img/flags/' . WP_MULTI_LANGUAGE_LANG .'.png" />
            <ul>' . $switcherLanguages . '</ul>
        ';

        $admin_bar->add_menu( array(
            'id'    => 'wp-ml-admin-switcher',
            'title' => $switcher,
            'href'  => '#',
            'meta'  => array(
                'title' => __('My Item'),
            ),
        ));
    }
}