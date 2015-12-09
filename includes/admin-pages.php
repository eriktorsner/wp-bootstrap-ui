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
    $viewData = new stdClass();
    $viewData->tabs = array(
        'Overview' => array(
            'slug' => 'overview',
        ),
        'File Appsettings.json' => array(
            'slug' => 'appsettings',
        ),
        'WP-CFM Settings' => array(
            'slug' => 'settings',
        ),
        'Export' => array(
            'slug' => 'export',
        ),
        'About' => array(
            'slug' => 'aboutthis',
        ),
    );

    // did we get a POST?
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        switch ($action) {
            case 'createsappettings':
                $mgrAppsettings = new wpbsui_AppsettingsManager();
                $mgrAppsettings->createFile($viewData);
                break;
            case 'createsettings':
                $mgrSettings = new wpbsui_SettingsManager();
                $mgrSettings->updateWPCFMSettings($viewData);
                break;
            case 'export':
                $mgrExport = new wpbsui_Export();
                $mgrExport->runExport($viewData);
                break;
        }
    }

    // select view, default to appsettings
    $viewData->tab_slug = 'overview';
    if (isset($_REQUEST['tab'])) {
        $viewData->tab_slug = $_REQUEST['tab'];
    }

    switch ($viewData->tab_slug) {
        case 'appsettings':
            if (!isset($mgrAppsettings)) {
                $mgrAppsettings = new wpbsui_AppsettingsManager();
            }
            $mgrAppsettings->initViewdata($viewData);
            break;
        case 'settings':
            if (!isset($mgrSettings)) {
                $mgrSettings = new wpbsui_SettingsManager();
            }
            $mgrSettings->initViewdata($viewData);
            break;
    }

    include WPBSUI_ROOT.'/views/admin-pages.php';
}
