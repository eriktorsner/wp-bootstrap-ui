<?php

function wpbsui_settings_menu()
{
    add_management_page('Wp-bootstrap UI', 'Wp-bootstrap', 'manage_options', 'wpbsui_page', 'custom_permalinks_options_page');
}

add_action('admin_menu', 'wpbsui_settings_menu');
