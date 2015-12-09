<form method="POST" action="tools.php?page=wpbsui_page&tab=settings">
    <input type="hidden" name="tab" value="settings">
    <input type="hidden" name="action" value="createsettings">

<table class="form-table">
    <tr>
        <th style="width: 7em;">Status</th>
        <th>Setting name</th>
        <th>Current value</th>
        <th>Default value</th>
        <th>Managed</th>
    </tr>


    <tr>
        <td colspan="5">
            <h3>Settings recommended for management</h3>
            <br>These settings should almost always be handled by WP-CFM
        </td>
    </tr>
    <?php
        $currentType = optionsDefaults::HINT_RECOMMENDED;
        include __DIR__.'/tab-settings-table.php';
    ?>
    <?php
        $currentType = optionsDefaults::HINT_PROBABLY;
        include __DIR__.'/tab-settings-table.php';
    ?>

    <tr>
        <td colspan="5">
            <h3>Standard settings</h3>
        </td>
    </tr>
    <?php
        $currentType = optionsDefaults::HINT_STANDARD;
        include __DIR__.'/tab-settings-table.php';
    ?>

    <tr>
        <td colspan="5">
            <h3>Settings NOT recommended for magagement (external)</h3>
            <br>These settings are managed by other mechanisms in Wp-Bootstrap and should not 
            be managed via WP-CFM
        </td>
    </tr>
    <?php
        $currentType = optionsDefaults::HINT_EXTERNALLY;
        include __DIR__.'/tab-settings-table.php';
    ?>


    <tr>
        <td colspan="5">
            <h3>Settings NOT recommended for magagement (internal)</h3>
            <br>These settings are managed internally by WordPress and should never be changed via WP-CFM
        </td>
    </tr>
    <?php
        $currentType = optionsDefaults::HINT_INTERNALLY;
        include __DIR__.'/tab-settings-table.php';
    ?>    

    <tr>
        <td colspan="5">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="4">
            Click save to add all checked settings to the WP-CFM bundle named 'wpbootstrap'. Adding them to 
            the bundle does not save them now. It means that next time you export your settings using 
            Wp-Bootstap Export or directly via WP-CFM, the values of those settings will be serialized into 
            a file that can be managed using source code control.
        </td>
        <td>
            <input type="submit" name="submit" id="submit" class="button button-primary" value="Save">
        </td>        
    </tr>    
</table>

</form>