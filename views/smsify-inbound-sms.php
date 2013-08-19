<?php if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
    exit ("Do not access this file directly.");

echo '<script>var apiEndpoint = "' . $params->apiEndpoint . '";var api_key = "' . $params->api_key . '";</script>';
echo '<script>var existing_app_user = true;</script>';
wp_enqueue_style('kendo-default');
wp_enqueue_style('kendo-common');
wp_enqueue_style('smsify');
wp_enqueue_script('smsify-common');
wp_enqueue_script('kendo-all');
wp_enqueue_script('kendo-controller');
wp_enqueue_script('settings-controller');
?>
<script>var smsifyCredits = <?php echo($credits) ?>;</script>
<div id="smsifywindow"></div>
<div id="icon-edit-comments" class="icon32"><br /></div>
<h2 class="smsify-inbound-title">Inbound SMS</h2>
<!-- CONTENT (start) -->
<div id="content" role="main">
    <?php if($credits) : ?>
    <div id="smsify-loading"><img src="<?php echo $params->cdnurl ?>/css/kendo/Default/loading-image.gif" alt="loading..." /><p>Loading SMSify</p></div>
        <div class="inner k-content smsify-inbound-app" style="display:none;">
    </div><!-- inner (end) -->
    <?php else : ?>
        <div class='error smsify-error'>We seem to have a little problem. Please check that you: <a href='admin.php?page=wp-smsify-settings'><br/>1. have entered the correct SMSify API Key on the Settings page.</a><br/>2. have enough credits to send at least one SMS. You can purchase more credits on <a href='http://www.smsify.com.au/pricing' target='_blank' title='Purchase SMSify credits'>SMSify website</a></div>
    <?php endif; ?>
</div><!-- #content (end) -->

<!-- CONTENT (end) -->