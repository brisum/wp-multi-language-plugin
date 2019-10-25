<?php
/**
Plugin Name: WP Multi Languages
Plugin URI: https://brisum.com
Description:
Version: 1
Author: Oleksandr Manchenko
Author URI: https://brisum.com
Text Domain: wp-multilanguages
Domain Path: /wp-multilanguages
License: MIT
 */

spl_autoload_register(function ($class) {
    $prefix = 'WPMultiLanguage\\';
    $base_dir = __DIR__ . '/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

if ( ! function_exists('build_url'))
{
    /**
     * @param array $parts
     * @return string
     */
    function build_url(array $parts)
    {
        $scheme   = isset($parts['scheme']) ? ($parts['scheme'] . '://') : '';
        $host     = $parts['host'] ?? '';
        $port     = isset($parts['port']) ? (':' . $parts['port']) : '';
        $user     = $parts['user'] ?? '';
        $pass     = isset($parts['pass']) ? (':' . $parts['pass'])  : '';
        $pass     = ($user || $pass) ? ($pass . '@') : '';
        $path     = $parts['path'] ?? '';
        $query    = isset($parts['query']) ? ('?' . $parts['query']) : '';
        $fragment = isset($parts['fragment']) ? ('#' . $parts['fragment']) : '';
        $url = implode('', [$scheme, $user, $pass, $host, $port, $path, $query, $fragment]);

        return isset($parts['scheme']) ? $url : ('/' . ltrim($url, '/'));
    }
}

function wp_multi_language_url_override($url, $lang)
{
    global $wp_multi_language;
    $langsAllRegExp = implode('|', $wp_multi_language['langs']);
    $urlParts = parse_url($url);

    $urlParts['path'] = preg_replace("/^\/?({$langsAllRegExp})(\/.+)?$/", '$2', $urlParts['path']);
    if (empty($urlParts['path'])) {
        $urlParts['path'] = '/';
    }
    if ($lang != $wp_multi_language['default_lang']) {
        $urlParts['path'] = "/{$lang}{$urlParts['path']}";
    }

    if (isset($urlParts['query'])) {
        $urlParts['query'] = preg_replace("/^(.*)&?lang=(?:{$langsAllRegExp})(.*)$/", '$1$2', $urlParts['query']);
        $urlParts['query'] = trim($urlParts['query'], '&');

        if (empty($urlParts['query'])) {
            unset($urlParts['query']);
        }
    }

    return build_url($urlParts);
}

define('WP_MULTI_LANGUAGE_VERSION', '1');
define('WP_MULTI_LANGUAGE_DIR', plugin_dir_path(__FILE__));
define('WP_MULTI_LANGUAGE_URL', plugin_dir_url(__FILE__));

require_once __DIR__ . '/settings.php';

new WPMultiLanguage\WPMultiLanguage();
new WPMultiLanguage\Plugin\Post();
new WPMultiLanguage\Plugin\Term();
new WPMultiLanguage\Plugin\TermTaxonomy();
new WPMultiLanguage\Plugin\Assets();
new WPMultiLanguage\Plugin\RewriteRule();

add_action( 'widgets_init', function(){
    register_widget('WPMultiLanguage\Widget\Switcher');
});

if (is_admin()) {
    new WPMultiLanguage\Plugin\AdminBar();
}

