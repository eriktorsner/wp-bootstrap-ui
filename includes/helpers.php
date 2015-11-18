<?php

function wpbsui_initFileSystem()
{
    // get file system access
    $url = wp_nonce_url('tools.php?page=wpbsui_page', 'wpbsui-page');
    if (false === ($creds = request_filesystem_credentials($url, '', false, false, null))) {
        return false;
    }
    if (!WP_Filesystem($creds)) {
        request_filesystem_credentials($url, '', true, false, null);

        return false;
    }

    return true;
}

function wpbsui_ensureContentFolderExists()
{
    global $wp_filesystem;
    if (!$wp_filesystem->is_dir(WPBSUI_CONTENT)) {
        /* directory didn't exist, so let's create it */
        $wp_filesystem->mkdir(WPBSUI_CONTENT);
    }
}

function wpbsui_getSelected($type)
{
    $ret = array();
    foreach ($_POST as $fldname => $value) {
        if (substr($fldname, 0, strlen($type)) == $type) {
            if ($value == 1) {
                $name = substr($fldname, strlen($type));
                $ret[$name] = $value;
            }
        }
    }

    return $ret;
}

function wpbsui_addPluginPropterties(&$plugins, $standardPlugins)
{
    $merged = array_merge($standardPlugins->response, $standardPlugins->no_update);
    foreach ($plugins as $key => &$plugin) {
        // set a default
        $info = pathinfo($key);
        $plugin['slug'] = $info['dirname'];
        $plugin['standard'] = false;

        if (isset($merged[$key])) {
            $plugin['slug'] = $merged[$key]->slug;
            $plugin['standard'] = true;
        } else {
            if (isset($plugin['PluginURI'])) {
                $uri = $plugin['PluginURI'];
                if (substr($uri, 0, 29) == 'http://wordpress.org/plugins/') {
                    $plugin['slug'] = substr($uri, 29);
                    $plugin['slug'] = rtrim($plugin['slug'], '/');
                }
            }
        }
    }
}
