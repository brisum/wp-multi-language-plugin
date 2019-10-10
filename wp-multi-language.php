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

require_once __DIR__ . '/settings.php';

new WPMultiLanguage\WPMultiLanguage();
new WPMultiLanguage\Plugin\Post();

