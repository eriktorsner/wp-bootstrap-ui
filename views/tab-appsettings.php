<form method="POST" action="tools.php?page=wpbsui_page">
    <input type="hidden" name="tab" value="appsettings">
    <input type="hidden" name="action" value="createsettings">

    <table class="form-table">
        <tbody>
            <tr>
                <hr>
                <th colspan="2"><h3>General</h3></th>
            </tr>
            <tr>
                <th scope="row">
                    <label for="blogname">Site title</label>
                </th>
                <td>
                    <input name="blogname" value="<?php echo $viewData->existingTitle ?>">
                </td>
            </tr>

            <tr>
                <th colspan="2">
                    <hr>
                    <h3>Themes and plugins</h3>
                </th>
            </tr>
            <tr>
                <td colspan="2">
                    <p>This is a list of all themes currently found in this WordPress installation. We're assuming that they
                        are all here for a reason, so the default is to include all of them. Please uncheck the themes that you
                        don't wish to include in the export.
                    </p>
                </td>
            </tr>

            <tr>
                <th>Existing themes</th>
                <td>
                    <?php foreach ($viewData->existingThemes as $key => $theme):?>
                        <?php
                            $selected = "";
                            if (@in_array($key, $viewData->appsettings->themes->standard)) {
                                $selected = "checked";
                            }
                        ?>

                        <input name="theme_<?php echo $key?>" type="checkbox" value="1" <?php echo $selected?>>
                        <label for="theme_<?php echo $key?>"><?php echo $theme->Name?> </label>&nbsp;&nbsp;
                    <?php endforeach ?>
                </td>
            </tr>
            <tr>
                <th>Active theme</th>
                <td>
                    <?php
                        $activeTheme = $activeTheme->Name;
                        if (isset($viewData->appsettings->themes->active)) {
                            $activeTheme = $viewData->appsettings->themes->active;
                        }
                    ?>
                    <select name="activeTheme">
                    <?php foreach ($viewData->existingThemes as $key => $theme):?>
                        <?php
                            $selected = "";
                            if ($key == $activeTheme) {
                                $selected = "selected";
                            }
                        ?>
                        <option value="<?php echo $key?>" <?php echo $selected?>><?php echo $theme->Name?></option>
                    <?php endforeach ?>
                    </select>
                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <p>This is a list of all plugins currently found in this WordPress installation. We're assuming that they
                       all plugins should be exported. Please uncheck the plugins that you don't wish to include in the export.
                    </p>
                </td>
            </tr>

            <tr>
                <th>Installed plugins</th>
                <td>
                    <?php foreach ($viewData->existingPlugins as $key => $plugin):?>
                        <?php
                            $selected = "";
                            if (@in_array($plugin['slug'], $viewData->appsettings->plugins->standard)) {
                                $selected = "checked";
                            }
                        ?>
                        <input name="plugin_<?php echo $plugin['slug']?>" type="checkbox" value="1" <?php echo $selected?>>
                        <label for="plugin_<?php echo $plugin['slug']?>"><?php echo $plugin['Name']?></label>&nbsp;&nbsp;

                    <?php endforeach ?>
                </td>
            </tr>

            <tr>
                <th colspan="2">
                    <hr>
                    <h3>Content</h3>
                </th>
            </tr>
            <tr>
                <td colspan="2">
                    <p>This is a list of all published posts in this WordPress installation. Remember that in this context, a posts
                        means anything that is stored in the wp_posts table. This includes posts and pages by default but depending on
                        your site it can also include products or other custom post types. <strong>NOTE:</strong> Media / attachments
                        (images etc) will be automatically included in the export if it's attached to a post that's already included.
                    </p>
                </td>
            </tr>
            <?php foreach ($viewData->allPosts as $postType):?>
                <tr>
                    <th>Post type: <?php echo $postType->post_type?></th>
                    <td>
                        <?php
                            $type = $postType->post_type;
                            $selected = "";
                            if (isset($viewData->appsettings->wpbootstrap->posts->$type)) {
                                if ($viewData->appsettings->wpbootstrap->posts->$type === '*') {
                                    $selected = "checked";
                                }
                            }
                        ?>
                        <input name="post_all_<?php echo $postType->post_type?>" type="checkbox" <?php echo $selected?> value="1">
                        <label for="post_all_<?php echo $postType->post_type?>">All (*)</label>&nbsp;&nbsp;

                        <?php foreach ($postType->posts as $post):?>
                            <?php
                                $selected = "";
                                if (isset($viewData->appsettings->wpbootstrap->posts->$type)) {
                                    if (in_array($post->name, $viewData->appsettings->wpbootstrap->posts->$type)) {
                                        $selected = "checked";
                                    }
                                }
                            ?>
                            <input name="post_<?php echo $postType->post_type?>_<?php echo $post->name?>" type="checkbox" value="1" <?php echo $selected?>>
                            <label for="post_<?php echo $postType->post_type?>_<?php echo $post->name?>">
                                <?php echo $post->title?> (<?php echo $post->name?> id:<?php echo $post->id?>)
                            </label>&nbsp;&nbsp;

                        <?php endforeach ?>
                    </td>
                </tr>
            <?php endforeach ?>

            <tr>
                <td colspan="2">
                    <hr>
                    <p>Select what taxonomy terms to include. Any unselected term that is a parent (or grand parent) of a selected
                        term will be included.</p>
                </td>
            </tr>
            <?php foreach ($viewData->allTaxonomies as $taxonomy):?>
                <tr>
                    <th>Taxonomy: <?php echo $taxonomy->name?></th>
                    <td>
                        <?php
                            $tax = $taxonomy->name;
                            $selected = "";
                            if (isset($viewData->appsettings->wpbootstrap->taxonomies->$tax)) {
                                if ($viewData->appsettings->wpbootstrap->taxonomies->$tax === '*') {
                                    $selected = "checked";
                                }
                            }
                        ?>
                        <input name="term_all_<?php echo $taxonomy->name?>" type="checkbox" <?php echo $selected?> value="1">
                        <label for="term_all_<?php echo $taxonomy->name?>">All (*)</label>&nbsp;&nbsp;

                        <?php foreach ($taxonomy->terms as $term):?>
                            <?php
                                $selected = "";
                                if (isset($viewData->appsettings->wpbootstrap->taxonomies->$tax)) {
                                    if (in_array($term->slug, $viewData->appsettings->wpbootstrap->taxonomies->$tax)) {
                                        $selected = "checked";
                                    }
                                }
                            ?>
                            <input name="term_<?php echo $taxonomy->name?>_<?php echo $term->slug?>" type="checkbox" value="1" <?php echo $selected?>>
                            <label for="term_<?php echo $taxonomy->name?>_<?php echo $term->slug?>"><?php echo $term->name?> </label>&nbsp;&nbsp;
                        <?php endforeach ?>
                    </td>
                </tr>
            <?php endforeach ?>

            <tr>
                <td colspan="2">
                    <hr>
                    <p>Select menus to include. When you mark a menu for inclusion, all it's items
                        will be included as well as the info about which locations the menu is used at.</p>
                </td>
            </tr>
            <tr>
                <th>Menus</th>
                <td>
                    <?php foreach ($viewData->menus as $menu):?>
                        <?php
                            $selected = "";
                            $menuSlug = $menu->slug;
                            if (isset($viewData->appsettings->wpbootstrap->menus->$menuSlug)) {
                                $selected = "checked";
                            }
                        ?>
                        <input name="menu_<?php echo $menuSlug?>" type="checkbox" value="1" <?php echo $selected?>>
                        <label for="menu_<?php echo $menuSlug?>"><?php echo $menu->name?> </label>&nbsp;&nbsp;
                    <?php endforeach ?>
                </td>
            </tr>


            <tr>
                <td colspan="2">
                    <hr>
                    <p>Select sidebars to include. When you mark a sidebar for inclusion, all widgets
                        in it will be included as well.</p>
                </td>
            </tr>
            <tr>
                <th>Sidebars</th>
                <td>
                    <?php foreach ($viewData->sidebars as $key => $sidebar):?>
                        <?php
                            $selected = "";
                            if (@in_array($key, $viewData->appsettings->wpbootstrap->sidebars)) {
                                $selected = "checked";
                            }
                        ?>
                        <input name="sidebar_<?php echo $key?>" type="checkbox" value="1" <?php echo $selected?>>
                        <label for="sidebar_<?php echo $key?>"><?php echo $sidebar['name']?> </label>&nbsp;&nbsp;
                    <?php endforeach ?>
                </td>
            </tr>






        </tbody>
    </table>

    <p class="submit">
        <input type="submit" name="submit" id="submit" class="button button-primary" value="Generate settings">
    </p>


</form>
