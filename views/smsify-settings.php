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
                <th><?php _e("SMS credits remaining"); ?>:</th>
                <td><?php _e($credits) ?></td>            
            </tr>
            <tr>
                <th><?php _e("API Key"); ?>:</th>
                <td><input type="text" class="regular-text" name="apiKey" id="apiKey" maxlength="50" value="<?php if($valid_key) { _e('--- Not shown ---'); } else { echo $api_key; }?>" /><?php if($valid_key) : ?><button id="smsify_toggle_key" class="button" onclick="toggleAPIKey(event, '--- Not shown ---','<?php echo $api_key; ?>', '<?php _e("Show") ?>', '<?php _e("Hide") ?>')">Show</button><?php endif; ?></td>            
            </tr>
            <?php if($valid_key) : ?>
            <tr>
                <th><label for="smsify-enable-sender-id-override"><?php _e("Enable Sender ID Override"); ?></label></th>
                <td>
                    <input type="checkbox" id="smsify-enable-sender-id-override" name="smsify-enable-sender-id-override"<?php if($smsify_enable_sender_id_override) echo ' checked="checked"'; ?> value="1" />
                    <span class="description"><?php _e("By default, SMSify will use a sender ID that's automatically assigned to you. If you tick this box, you will be able to override your default sender ID, provided you have purchased one from SMSify. Use this feature with caution. If you specify an invalid Sender ID, your SMS will not be delivered and you may be charged 1 SMS credit regardless."); ?></span>
                </td>
            </tr>
            <?php endif ?>
            <tr>
                <?php if($valid_key) : ?>
                    <td><input type="submit" name="deactivate" id="deactivate" class="button action" value="DEACTIVATE"></td>
                    <td><input type="submit" name="update" id="update" class="button action" value="UPDATE"></td>
                <?php else : ?>
                    <td colspan="2"><input type="submit" name="activate" id="activate" class="button action" value="ACTIVATE"></td>
                <?php endif ?>
            </tr>
        </tbody>
    </table>
</form>