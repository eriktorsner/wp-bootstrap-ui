<?php

require_once WPBSUI_ROOT.'/vendor/autoload.php';
define('WPBSUI_CONTENT', WP_CONTENT_DIR.'/wp-bootstrap');

function wpbsui_settings_menu()
{
    add_management_page('Wp-bootstrap UI', 'Wp-bootstrap', 'manage_options', 'wpbsui_page', 'wpbsui_mainpage');
}
add_action('admin_menu', 'wpbsui_settings_menu');

function wpbsui_mainpage()
{
    $mgrAppsettings = new wsbui_AppsettingsManager();
    $viewData = new stdClass();
    $viewData->tabs = array(
        'Appsettings' => array(
            'slug'       => 'appsettings',
        ),
        'Export' => array(
            'slug'       => 'export',
        ),
        'About' => array(
            'slug'       => 'aboutthis',
        ),
    );

    // did we get a POST?
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        switch ($action) {
            case 'createsettings':
                $mgrAppsettings->createFile($viewData);
                break;
        }
    }

    // select view, default to appsettings
    $viewData->tab_slug = 'appsettings';
    if (isset($_REQUEST['tab'])) {
        $viewData->tab_slug = $_REQUEST['tab'];
    }

    switch ($viewData->tab_slug) {
        case 'appsettings':
            $mgrAppsettings->initViewdata($viewData);
            break;
    }

    include WPBSUI_ROOT.'/views/admin-pages.php';
}
