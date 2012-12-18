<?php
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
    exit ("Do not access this file directly.");
function smsify_getConfig() {
    global $params;
    global $current_user;
    $params = new stdClass();
    $params->appVersion = '3.0.1';
    $params->api_key = get_user_meta($current_user->ID, 'smsify-api-key', true);
    $params->apihost = 'www.smsify.com.au';
    $params->cdnurl = 'https://' . $params->apihost;
    $params->apiEndpoint = 'https://' . $params->apihost;
    $params->cssurl = '/' . PLUGINDIR . '/smsify/css';
    $params->jsurl = '/' . PLUGINDIR . '/smsify/js';
    $params->imageurl = '/' . PLUGINDIR . '/smsify/images';
    $params->smsifydir = $_SERVER["DOCUMENT_ROOT"] . '/' . PLUGINDIR . '/smsify';
    
    wp_register_script('kendo-all', 
                        $params->cdnurl . '/wp-includes/js/kendo/min/kendo.all.min.js', 
                        array(), 
                        $params->appVersion);
    wp_register_script('smsify-common', 
                        $params->jsurl . '/common.js', 
                        array(), 
                        $params->appVersion);
    wp_register_script('kendo-controller', 
                        $params->jsurl . '/kendocontroller.min.js', 
                        array('smsify-common','kendo-all'),
                        $params->appVersion);
    wp_register_script('settings-controller', 
                        $params->jsurl . '/settingscontroller.min.js', 
                        array('kendo-all'), 
                        $params->appVersion);
    wp_register_style('kendo-default', 
                        $params->cdnurl . '/wp-includes/css/kendo/kendo.default.min.css', 
                        array(), 
                        $params->appVersion,
                        'all');
    wp_register_style('kendo-common', 
                        $params->cdnurl . '/wp-includes/css/kendo/kendo.common.min.css', 
                        array(), 
                        $params->appVersion,
                        'all');
    wp_register_style('smsify', 
                        $params->cssurl . '/smsify.css', 
                        array(), 
                        $params->appVersion,
                        'all');
    return $params;
}

function smsify_checkCredits() {
	global $params;
	// Get credits for this user and check that API key is good
    if(!$credits = intval(trim(file_get_contents($params->apiEndpoint . '/transport/?method=getCreditsRaw&key=' . $params->api_key . '&version=latest')))) {
        // If another wordpress site using this plugin
        if($_SERVER['SERVER_NAME'] != $params->apihost) {    
            echo "<div class='error smsify-error'>We seem to have a little problem. Please check that you: <a href='admin.php?page=wp-smsify-settings'><br/>1. have entered the correct SMSify API Key on the Settings page.</a><br/>2. have enough credits to send at least one SMS. You can purchase more credits on <a href='http://www.smsify.com.au/pricing' target='_blank' title='Purchase SMSify credits'>SMSify website</a></div>";    
    	}
    }
	return $credits;
}	
?>