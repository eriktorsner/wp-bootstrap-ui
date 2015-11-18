<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @author    Torgesta Technology <info@torgesta.com>
 * @license   GPL-2.0+
 *
 * @link      http://www.torgesta.com
 *
 * @copyright 2014 Torgesta Technology
 */
?>

<div class="wrap">

    <div class="wrap">
        <h2><?php echo esc_html(get_admin_page_title()); ?></h2>

        <h2 class="nav-tab-wrapper">
        <?php foreach ($viewData->tabs as $name => $tab): ?>
            <?php $class = ($tab['slug'] == $viewData->tab_slug) ? 'nav-tab-active' : ''; ?>
            <a class="nav-tab <?php echo $class?>" href="?page=<?php echo $plugin_slug?>&amp;tab=<?php echo $tab['slug']?>"><?php echo $name?></a>
        <?php endforeach ?>

        <?php include 'tab-'.$viewData->tab_slug.'.php'; ?>

    </div>



</div>
