<?php
class wsbui_AppsettingsManager
{
    private $excludedTypes = array('revision', 'nav_menu_item', 'attachment');
    private $excludedTaxonomies = array('nav_menu', 'link_category', 'post_format');

    public function createFile(&$viewData)
    {
        global $wp_filesystem, $wp_registered_sidebars;

        if (!wpbsui_initFileSystem()) {
            return;
        }
        wpbsui_ensureContentFolderExists();

        $bstrp = new Wpbootstrap\Bootstrap();
        $appsettings = new stdClass();

        // title
        $appsettings->title = sanitize_text_field($_POST['blogname']);

        // plugins
        $appsettings->plugins = new stdClass();
        $appsettings->plugins->standard = array();
        $selected = wpbsui_getSelected('plugin_');
        foreach ($selected as $name => $value) {
            $appsettings->plugins->standard[] = $name;
        }

        // themes
        $appsettings->themes = new stdClass();
        $appsettings->themes->standard = array();
        $selected = wpbsui_getSelected('theme_');
        foreach ($selected as $name => $value) {
            $appsettings->themes->standard[] = $name;
        }
        $appsettings->themes->active = $_POST['activeTheme'];

        $wpbootstrap = new stdClass();

        // posts
        $wpbootstrap->posts = new stdClass();
        $postTypes = get_post_types();
        foreach ($postTypes as $type) {
            if (in_array($type, $this->excludedTypes)) {
                continue;
            }

            if (isset($_POST['post_all_'.$type]) && $_POST['post_all_'.$type] == 1) {
                $wpbootstrap->posts->$type = '*';
            } else {
                $selected = wpbsui_getSelected('post_'.$type.'_');
                if (count($selected) > 0) {
                    $wpbootstrap->posts->$type = array();
                    foreach ($selected as $postName => $value) {
                        array_push($wpbootstrap->posts->$type, $postName);
                    }
                }
            }
        }
        if (count((array) $wpbootstrap->posts) == 0) {
            unset($wpbootstrap->posts);
        }

        // taxonomies
        $wpbootstrap->taxonomies = new stdClass();
        $taxonomies = get_taxonomies();
        foreach ($taxonomies as $taxonomy) {
            if (in_array($taxonomy, $this->excludedTaxonomies)) {
                continue;
            }

            if (isset($_POST['term_all_'.$taxonomy]) && $_POST['term_all_'.$taxonomy] == 1) {
                $wpbootstrap->taxonomies->$taxonomy = '*';
            } else {
                $selected = wpbsui_getSelected('term_'.$taxonomy.'_');
                if (count($selected) > 0) {
                    $wpbootstrap->taxonomies->$taxonomy = array();
                    foreach ($selected as $postName => $value) {
                        array_push($wpbootstrap->taxonomies->$taxonomy, $postName);
                    }
                }
            }
        }
        if (count((array) $wpbootstrap->taxonomies) == 0) {
            unset($wpbootstrap->taxonomies);
        }

        // menus
        $wpbootstrap->menus = new stdClass();
        $locations = get_theme_mod('nav_menu_locations');
        $menus = get_terms('nav_menu', array( 'hide_empty' => true ));
        $selected = wpbsui_getSelected('menu_');
        foreach ($menus as $menu) {
            $menuName = $menu->name;
            if (isset($selected[$menuName])) {
                $wpbootstrap->menus->$menuName = array();
                foreach ($locations as $name => $location) {
                    if ($location == $menu->term_id) {
                        array_push($wpbootstrap->menus->$menuName, $name);
                    }
                }
            }
        }

        // sidebars
        $wpbootstrap->sidebars = array();
        $selected = wpbsui_getSelected('sidebar_');
        foreach ($wp_registered_sidebars as $key => $sidebar) {
            if (isset($selected[$key])) {
                $wpbootstrap->sidebars[] = $key;
            }
        }
        if (count((array) $wpbootstrap->sidebars) == 0) {
            unset($wpbootstrap->sidebars);
        }

        $appsettings->wpbootstrap = $wpbootstrap;
        $out = $bstrp->prettyPrint(json_encode($appsettings));

        $file = WPBSUI_CONTENT.'/appsettings.json';
        $wp_filesystem->put_contents($file, $out, false);
    }

    public function initViewdata(&$viewData)
    {
        global $wp_registered_sidebars;

        $file = WPBSUI_CONTENT.'/appsettings.json';
        if (file_exists($file)) {
            $viewData->appsettings = json_decode(file_get_contents($file));
        } else {
            $viewData->appsettings = new stdClass();
        }

        wp_update_plugins(false);
        wp_update_themes(false);
        $standardPlugins = get_option('_site_transient_update_plugins', $default);
        $standardThemes = get_option('_site_transient_update_themes', $default);

        $viewData->existingTitle = get_bloginfo('name');

        // themes
        $viewData->existingThemes = wp_get_themes();
        $viewData->activeTheme = wp_get_theme();

        // plugins
        $viewData->existingPlugins = get_plugins();
        wpbsui_addPluginPropterties($viewData->existingPlugins, $standardPlugins);

        // posts
        $viewData->allPosts = array();
        $postTypes = get_post_types();
        foreach ($postTypes as $type) {
            if (in_array($type, $this->excludedTypes)) {
                continue;
            }

            $postType = new stdClass();
            $postType->post_type = $type;
            $postType->posts = array();

            $args = array('post_type' => $type, 'posts_per_page' => -1, 'post_status' => 'publish');
            $posts = get_posts($args);
            foreach ($posts as $post) {
                $item = new stdClass();
                $item->id = $post->ID;
                $item->name = $post->post_name;
                $item->title = $post->post_title;
                $postType->posts[] = $item;
            }
            $viewData->allPosts[$type] = $postType;
        }

        // menus
        $viewData->menus = get_terms('nav_menu', array( 'hide_empty' => true ));

        // Taxonomies
        $viewData->allTaxonomies = array();
        $taxonomies = get_taxonomies();

        foreach ($taxonomies as $taxonomy) {
            if (in_array($taxonomy, $this->excludedTaxonomies)) {
                continue;
            }

            $tax = new stdClass();
            $tax->name = $taxonomy;
            $tax->terms = array();

            $terms = get_terms($taxonomy, array( 'hide_empty' => false ));
            foreach ($terms as $term) {
                $item = new stdClass();
                $item->id = $term->term_id;
                $item->name = $term->name;
                $item->slug = $term->slug;
                $tax->terms[] = $item;
            }
            $viewData->allTaxonomies[] = $tax;
        }

        // Sidebars.
        $viewData->sidebars = $wp_registered_sidebars;
    }
}
