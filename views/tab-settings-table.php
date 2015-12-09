<?php foreach ($viewData->changeSet as $setting): ?>
    <?php
        if ($setting->meta['type'] != $currentType) {
            continue;
        }
    ?>
    <?php
        $status = 'unknown';
        $rowStyle = '';
        switch ($setting->state) {
            case optionsDefaults::STATE_UNCHANGED:
                $status = '';
                break;
            case optionsDefaults::STATE_MODIFIED:
                $status = 'Modified';
                $rowStyle = 'style="background-color: bisque;"';
                break;
            case optionsDefaults::STATE_NEW:
                $status = 'New';
                $rowStyle = 'style="background-color: aliceblue;"';
                break;
        }
    ?>    
    <tr <?php echo $rowStyle?>>
        <td><?php echo $status;?></td>
        <td><?php echo $setting->name ?></td>
        <td>
            <?php 
                $orgValue = $setting->current;
                $value = htmlspecialchars($orgValue);
                if (is_serialized($orgValue)) {
                    $value = 'SERIALIZED DATA';
                }
            ?>
            <input class="regular-text all-options" type="text" style="width: 180px;"
                id="current_<?php echo $setting->name ?>" disabled value="<?php echo $value ?>">
        </td>
        <td>
            <?php 
                $orgValue = $setting->default;
                $value = htmlentities($orgValue);
                if (is_serialized($orgValue)) {
                    $value = 'SERIALIZED DATA';
                }
            ?>
            <input class="regular-text all-options" type="text" style="width: 180px;"
                id="current_<?php echo $setting->name ?>" disabled value="<?php echo $value ?>">
        </td>
        <td>
            <?php
                $checked = '';
                if (in_array($setting->name, $viewData->wpbBundle->config)) {
                    $checked = 'checked';
                }
            ?>
            <input name="managed_<?php echo $setting->name?>" type="checkbox" value="1" <?php echo $checked?>>
        </td>
    </tr>
<?php endforeach ?>
