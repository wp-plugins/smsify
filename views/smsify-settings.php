<?php
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
    exit ("Do not access this file directly.");
global $current_user;
$successMessage = "";
wp_enqueue_style('kendo-common');
wp_enqueue_style('kendo-default');
wp_enqueue_style('smsify');
wp_enqueue_script('settings-controller');

if(isset($_POST['apiKey'])) {
    if(strlen(trim($_POST['apiKey'])) == 32) {
        $apiKeyOption = trim($_POST['apiKey']);
        update_site_option('smsify-api-key', $apiKeyOption);
        $successMessage = "Your API Key has been updated successfully. <a href='admin.php?page=wp-smsify-app' title='Send SMS'>You may start SMSifying!</a>";
    }
} else {
    $apiKeyOption = get_site_option('smsify-api-key', false);
    // If not in site metadata, try user metadata for old versions.
    if(!$apiKeyOption) {
        $apiKeyOption = get_user_meta($current_user->ID, 'smsify-api-key', true);
        if($apiKeyOption) {
            update_site_option('smsify-api-key', $apiKeyOption);
        }
    }
}

if($apiKeyOption) {
    echo '<script>var existing_app_user = true;</script>';
} else {
    echo '<script>var existing_app_user = false;</script>';
}
?>
<div id="smsifywindow2"></div>
<div class="wrap columns-2 dd-wrap">
    <div id="icon-edit-pages" class="icon32 icon32-posts-page"><br /></div>
    <h2>SMSify - Global Settings</h2>
    <?php if(!$apiKeyOption && $_SERVER['SERVER_NAME'] != $params->apihost) smsify_show_error(true) ?>
    <div id="poststuff" class="metabox-holder has-right-sidebar">
        <?php include('smsify-sidebar.php'); ?>
        <div class="post-body">
            <div class="post-body-content">        
                <div class="stuffbox" style="margin-right:300px;">
                    <h3><label for="link_box">API Key</label></h3>
                    <div class="inside">    
                        <div id="api-key-container" role="main">
                            <?php if($successMessage) : ?>
                            <div class="update-nag"> <?php echo $successMessage; ?></div>
                            <?php endif; ?>
                            <form name="key-form" id="key-form" method="POST">
                                <?php if(!$apiKeyOption) : ?>  
                                    <div class="smsify-key-button">
                                        <a href="#" class="k-button" id="smsify-get-key">
                                          Get API Key Here
                                        </a>
                                    </div>
                                <?php endif ?>
                                <fieldset>
                                    <label for="apiKey">API key:</label> 
                                    <span class="api-key-edit">
                                        <input type="text" name="apiKey" id="apiKey" maxlength="50" size="40" value="<?php echo $apiKeyOption; ?>" />
                                        <a class="k-button k-button-icontext k-grid-cancel-changes smsify-cancel-key" href="#"><span class="k-icon k-cancel"></span>Cancel</a>
                                        <a class="k-button k-button-icontext k-grid-save-changes smsify-save-key" href="#"><span class="k-icon k-update"></span>Save</a>
                                    </span>
                                    <?php if($apiKeyOption) :  ?>  
                                        <span class="api-key-ro">
                                            <span> -- Not shown --</span>
                                            <a class="k-button k-button-icontext k-grid-edit-changes smsify-edit-key" href="#"><span class="k-icon k-edit"></span>Edit</a>
                                        </span>
                                    <?php endif ?>
                                    <span class="api-key-saving"><img src="<?php echo $params->cdnurl ?>/css/kendo/Default/loading-image.gif" alt="saving..." /></span>
                                </fieldset>
                            </form>
                        </div>
                    </div><!-- inside //-->
                 </div><!-- end stuffbox //-->
                 <?php /* if($apiKeyOption) :  ?>  
                     <div class="stuffbox" style="margin-right:300px;">
                        <h3><label for="link_box">Inbound SMS</label></h3>
                        <div class="inside"> 
                            <div id="smsify-inbound-container" role="main">
                                <form name="inbound-form" id="inbount-form">
                                    <label for="enable-inbound-sms"><input type="checkbox" name="enable-inbound-sms" id="enable-inbound-sms" value="1" /> Enable Inbound SMS</label>
                                    <a class="k-button k-button-icontext k-grid-save-changes smsify-inbound-sms-btn" href="#"><span class="k-icon k-update"></span>Enable Inbound SMS</a>
                                </form>
                                <span class="sms-inbound-saving"><img src="<?php echo $params->cdnurl ?>/css/kendo/Default/loading-image.gif" alt="saving..." /></span>
                           </div>
                       </div>
                    </div>
               <?php endif */ ?>
            </div><!-- end post-body-content //-->
        </div><!-- end post-body //-->
    </div><!-- end poststuff //-->
</div><!-- end wrap columns-2 dd-wrap //-->