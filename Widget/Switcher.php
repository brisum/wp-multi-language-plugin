<?php

namespace WPMultiLanguage\Widget;

use WP_Widget;

class Switcher extends WP_Widget {
    public function __construct()
    {
        $widget_options = array(
            'classname' => 'wp_ml_switcher',
            'description' => '',
        );

        parent::__construct('wp_ml_switcher', 'WP Multi Language - Switcher', $widget_options);
    }

    // output the widget content on the front-end
    public function widget( $args, $instance )
    {
        global $wp, $wp_multi_language;
        $langsAllRegExp = implode('|', $wp_multi_language['langs']);

        $url = $wp->request ? $wp->request : $_SERVER['REQUEST_URI'];
        $urlParts = parse_url($url);

        $switcherLanguages = [];
        foreach ($wp_multi_language['langs'] as $lang) {
            if (WP_MULTI_LANGUAGE_LANG == $lang) {
                continue;
            }

            $urlParts['path'] = preg_replace("/^\/?({$langsAllRegExp})(\/.+)?$/", '$2', $urlParts['path']);
            if (empty($urlParts['path'])) {
                $urlParts['path'] = '/';
            }
            if ($lang != $wp_multi_language['default_lang']) {
                $urlParts['path'] = "/{$lang}{$urlParts['path']}";
            }

            if (!empty($_GET)) {
                $urlParts['query'] = $_GET;
                unset($urlParts['query']['lang']);
                $urlParts['query'] = http_build_query($urlParts['query']);
            }

            $switcherLanguages[] = sprintf(
                '<a href="%s"><img src="' . WP_MULTI_LANGUAGE_URL . 'assets/img/flags/%s.png" alt=""></a>',
                build_url($urlParts),
                $lang
            );
        }
        $switcherLanguages = implode('', $switcherLanguages);
        $switcher = '
            <img src="' . WP_MULTI_LANGUAGE_URL . 'assets/img/flags/' . WP_MULTI_LANGUAGE_LANG .'.png" />
            <ul>' . $switcherLanguages . '</ul>
        ';

        ?>
            <div class="wp-ml-switcher">
                <?php echo $switcher; ?>
            </div>
        <?php
    }

    // output the option form field in admin Widgets screen
    public function form( $instance )
    {

    }

    // save options
    public function update( $new_instance, $old_instance )
    {

    }
}

