<?php

namespace WPMultiLanguage\Plugin;

class Assets
{
    public function __construct()
    {
        add_action('admin_enqueue_scripts', [$this, 'actionAdminEnqueueScripts'], 100);
        add_action('wp_enqueue_scripts', [$this, 'actionEnqueueScripts'], 100);
    }

    function actionAdminEnqueueScripts() {
        wp_enqueue_style('wp_multi_language_admin_style', WP_MULTI_LANGUAGE_URL . 'assets/dist/wp_multi_language_admin.css', [], WP_MULTI_LANGUAGE_VERSION);
        wp_enqueue_script('wp_multi_language_admin_script', WP_MULTI_LANGUAGE_URL . 'assets/dist/wp_multi_language_admin_script.js', ['jquery'], WP_MULTI_LANGUAGE_VERSION, true);
    }

    function actionEnqueueScripts() {
        wp_enqueue_style('wp_multi_language_style', WP_MULTI_LANGUAGE_URL . 'assets/dist/wp_multi_language.css', [], WP_MULTI_LANGUAGE_VERSION);
        wp_enqueue_script('wp_multi_language_script', WP_MULTI_LANGUAGE_URL . 'assets/dist/wp_multi_language_script.js', ['jquery'], WP_MULTI_LANGUAGE_VERSION, true);
    }
}
