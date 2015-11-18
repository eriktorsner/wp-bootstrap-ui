<?php
/*
Plugin Name: Wp-bootstrap UI
Plugin URL: https://github.com/eriktorsner/wp-bootstrap-ui
Description: User interface for creating settings files for Wp-bootstrap
Version: 0.1.0
Author: Erik Torsner
Author URI: http://erik.torgesta.com
Text Domain: wpbstrap
Domain Path: languages
*/

if (!defined('WPBSUI_PLUGIN_VERSION')) {
    define('WPBSUI_PLUGIN_VERSION', '0.1.0');
}

if (version_compare(PHP_VERSION, '5.3', '<')) {
    add_action('admin_notices', 'wpbsui_below_php_version_notice');
    function wpbsui_below_php_version_notice()
    {
        echo '<div class="error"><p>'.__('Your version of PHP is below the minimum version of PHP required by Wp-bootstrap UI. Please contact your host and request that your version be upgraded to 5.3 or later.', 'rcp').'</p></div>';
    }
} else {
    if (is_admin()) {
        define('WPBSUI_ROOT', dirname(__FILE__));
        require_once 'includes/admin-pages.php';
        require_once 'includes/helpers.php';
        require_once 'includes/appsettings.php';
    }
}
