<?php
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
    exit ("Do not access this file directly.");
function smsify_getConfig() {
    global $params;
    global $current_user;
    $params = new stdClass();
    $params->appVersion = '3.0.8';
    $params->api_key = get_site_option('smsify-api-key', false);
    $params->apihost = 'www.smsify.com.au';
    $params->cdnurl = 'https://d2c8ezxpvufza0.cloudfront.net';
    $params->apiEndpoint = 'https://' . $params->apihost;
    $params->cssurl = '/' . PLUGINDIR . '/smsify/css';
    $params->jsurl = '/' . PLUGINDIR . '/smsify/js';
    $params->imageurl = '/' . PLUGINDIR . '/smsify/images';
    $params->smsifydir = $_SERVER["DOCUMENT_ROOT"] . '/' . PLUGINDIR . '/smsify';
    
    wp_register_script('kendo-all', 
                        $params->cdnurl . '/js/kendo/min/1.0.0/kendo.all.min.js', 
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
                        $params->cdnurl . '/css/kendo/1.0.0/kendo.bootstrap.min.css', 
                        array(), 
                        $params->appVersion,
                        'all');
    wp_register_style('kendo-common', 
                        $params->cdnurl . '/css/kendo/1.0.0/kendo.common.min.css', 
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

function smsify_ping() {
    global $params;
    $ping = @file_get_contents($params->apiEndpoint . '/transport/?method=ping&key=' . $params->api_key . '&version=latest');
    return $ping;
}

function smsify_checkCredits() {
	global $params;
	// Get credits for this user.
    $credits = intval(trim(file_get_contents($params->apiEndpoint . '/transport/?method=getCreditsRaw&key=' . $params->api_key . '&version=latest')));

	return $credits;
}

function smsify_show_error($is_settings_page=false) {
    echo '<div class="error smsify-error" style="max-width:550px;float:left;">';
    echo '<p>You don\'t seem to have any SMS credits on your account.</p>'; 
    echo '<p>If this is your first time using SMSify, get your API key by creating an SMSify account on <a href="http://www.smsify.com.au/?ref=wp" target="_blank" title="SMSify - Get your message accross">www.smsify.com.au</a> or by clicking the button below.</p>'; 
    echo '<p>If you already have your SMSify key, make sure it is correct and that you have enough credits on your account.</p>';
    echo '<p>&nbsp;</p>';
    echo '<div class="smsify-key-button">';
    echo '<a href="#" class="k-button" id="smsify-get-key">GET YOUR API KEY HERE</a>';
    echo '</div>';
    if(!$is_settings_page) {
        echo '<h2>OR</h2>';
        echo '<div class="smsify-key-button">';
        echo '<a href="admin.php?page=wp-smsify-settings" class="k-button" id="smsify-get-key">';
        echo 'ENTER YOUR API KEY HERE';
        echo '</a>';
        echo '</div>';
    }
    echo '<p>&nbsp;</p>';
    echo '</div>';
}	
?>