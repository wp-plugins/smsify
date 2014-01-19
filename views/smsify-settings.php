<?php
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
    exit ("Do not access this file directly.");
?>
<h2><?php _e("SMSify Settings"); ?></h2>
<div<?php if($valid_key && strlen($validationMessage)) { echo ' class="updated"';}else{ echo ' class="error"';}?>><p><?php _e($validationMessage); ?></p></div>
<form name="key-form" id="key-form" method="POST">
    <table class="form-table">
        <tbody>
            <tr>
                <th><?php _e("API Key"); ?>:</th>
                <td><input type="text" class="regular-text" name="apiKey" id="apiKey" maxlength="50" value="<?php if($valid_key) { _e('--- Not shown ---'); } else { echo $api_key; }?>" /></td>            
            </tr>
            <tr>
                <?php if($valid_key) : ?>
                    <td colspan="2"><input type="submit" name="deactivate" id="deactivate" class="button action" value="DEACTIVATE"></td>
                <?php else : ?>
                    <td colspan="2"><input type="submit" name="activate" id="activate" class="button action" value="ACTIVATE"></td>
                <?php endif ?>
            </tr>
        </tbody>
    </table>
</form>