<?php
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
    exit ("Do not access this file directly.");
global $current_user;
$errorMessage = "";
$successMessage = "";
wp_enqueue_style('smsify');
if(isset($_POST['apiKey'])) {
    if(strlen(trim($_POST['apiKey'])) == 32) {
        $apiKeyOption = trim($_POST['apiKey']);
        update_user_meta($current_user->ID, 'smsify-api-key', $apiKeyOption);
        $successMessage = "API key updated successfully. Have fun!";
    } else {
        $errorMessage = "Invalid API key";        
    }
} else {
    $apiKeyOption = get_user_meta($current_user->ID, 'smsify-api-key', true);
}
?>
<div class="wrap columns-2 dd-wrap">
    <div id="icon-edit-pages" class="icon32 icon32-posts-page"><br /></div>
    <h2>SMSify - Global Settings</h2>
    <div id="poststuff" class="metabox-holder has-right-sidebar">
        <?php include('smsify-sidebar.php'); ?>
        <div class="post-body">
            <div class="post-body-content">        
                <div class="stuffbox" style="margin-right:300px;">
                    <h3><label for="link_box">API Key</label></h3>
                    <div class="inside">    
                        <div class="smsify-api-container">
                            <?php if(strlen($errorMessage)) : ?>
                                <div class="smsify-error"><?php echo $errorMessage; ?></div>
                            <?php endif ?>
                            <?php if(strlen($successMessage)) : ?>
                                <div class="smsify-success"><?php echo $successMessage; ?></div>
                            <?php endif ?>
                            <form name="key-form" method="POST">
                                <fieldset>
                                    <label for="apiKey">API key: <input type="text" name="apiKey" id="apiKey" maxlength="50" size="40" value="<?php echo $apiKeyOption; ?>" /></label>
                                    <input class="button-primary" type="submit" name="submit" id="submit" value="SAVE" />
                                </fieldset>
                            </form>
                            <p>In order to talk to SMSify gateway, you will need to enter your API key here.  Please <a href="<?php echo $params->apiEndpoint; ?>/my-account" title="Log into SMSify" target="_blank">log into your SMSify account</a> and <a href="<?php echo $params->apiEndpoint; ?>/api-key" title="Get your SMSify API key" target="_blank">get your API key</a>.</p>
                        </div><!-- end smsify-api-container //-->
                    </div><!-- inside //-->
                 </div><!-- end stuffbox //-->
            </div><!-- end post-body-content //-->
        </div><!-- end post-body //-->
    </div><!-- end poststuff //-->
</div><!-- end wrap columns-2 dd-wrap //-->