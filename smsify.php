<?php
/**
 *
 * Copyright: © 2014
 * {@link http://www.smsify.com.au/ SMSify.}
 * ( coded in Australia )
 *
 * Released under the terms of the GNU General Public License.
 * You should have received a copy of the GNU General Public License,
 * along with this software. In the main directory, see: /licensing/
 * If not, see: {@link http://www.gnu.org/licenses/}. 
 *
 * @package SMSify
 * @version 5.0.0
 */
/*
Plugin Name: SMSify
Plugin URI: http://www.smsify.com.au/
Description: <strong>SMSify</strong> is a premium SMS plugin that allows you to <strong>send and receive SMS</strong> within your own WordPress dashboard. SMSify allows you to <strong>import contacts</strong> from a csv file and <strong>schedule recurring SMS messages</strong>.  It features a native WordPress interface that is very simple to use. Screenshots available.  
Author: SMSify
Version: 5.0.0
Author URI: http://www.smsify.com.au/
*/

if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit ("Do not access this file directly.");

require_once 'includes/functions.php';
require_once 'modules/usergroups/UserGroupsExtended.php';
require_once 'modules/importusers/import-users-from-csv.php';

add_action( 'admin_menu', 'smsify_menu' );
function smsify_menu() {
	
	$smsify_page = add_menu_page( 'SMSify', 'SMSify', 'manage_options', 'smsify-settings', null, get_site_url() . '/wp-content/plugins/smsify/images/smsify-red-16x16.png' );
	$smsify_page_settings = add_submenu_page( 'smsify-settings', 'SMSify - settings', 'Settings', 'manage_options', 'smsify-settings', 'smsify_settings');
	$smsify_page_groups = add_submenu_page( 'smsify-settings', 'SMSify - groups', 'User Groups', 'manage_options', 'edit-tags.php?taxonomy=user-group');
	$smsify_page_responses = add_submenu_page( 'smsify-settings', 'SMSify - responses', 'Responses', 'manage_options', 'smsify-responses', 'smsify_responses');
	$smsify_page_schedules = add_submenu_page( 'smsify-settings', 'SMSify - schedules', 'Schedules', 'manage_options', 'smsify-schedules', 'smsify_schedules');
	$smsify_page_reporting = add_submenu_page( 'smsify-settings', 'SMSify - reporting', 'Reporting', 'manage_options', 'smsify-reporting', 'smsify_reporting');
}

function smsify_settings() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	$smsify_params = smsify_getConfig();
	wp_enqueue_script('smsify-sms-controller');
	$validationMessage = "";
	$valid_key = false;
	$smsify_enable_sender_id_override = false;
	$credits = 0;

	if(isset($_POST['activate'])) {
		if(strlen(trim($_POST['apiKey'])) == 32) {
			$api_key = trim($_POST['apiKey']);
			$response = smsify_get_credits($api_key);
			if ( is_wp_error( $response ) ) {
				$validationMessage = $response->get_error_message();
			}else if($response['response']['code'] != 200) {
				$validationMessage = __($response['body']);
			} else {
				$credits = json_decode($response['body'])->result;

				update_site_option('smsify-api-key', $api_key);
				if($response->result > 0) {    
					$validationMessage = __("Your API Key has been validated successfully.");
				} else {
					$validationMessage = __("Your API Key has been validated successfully, but you don't seem to have any credits on your account. Please recharge your account to continue sending SMS.");
				}
				$valid_key = true;
			}
		}
	} else if(isset($_POST['deactivate'])) {
		delete_site_option('smsify-api-key');
		delete_site_option('smsify_enable_sender_id_override');
		$validationMessage = __("Before you start using SMSify, please activate the plugin by pasting your API Key in the text field provided.");
	} else if(isset($_POST['update'])) {
		if(strlen($_POST['apiKey']) == 32) {
			update_site_option('smsify-api-key', $_POST['apiKey']);
			$response = smsify_get_credits($_POST['apiKey']);
			$credits = json_decode($response['body'])->result;
		}        
		if(isset($_POST['smsify-enable-sender-id-override'])) {
			update_site_option('smsify-enable-sender-id-override', 1);
			$smsify_enable_sender_id_override = true;
		} else {
			update_site_option('smsify-enable-sender-id-override', 0);
			$smsify_enable_sender_id_override = false;            
		}    
		if($_POST['apiKey'] != '--- Not shown ---' && strlen($_POST['apiKey']) != 32) {
			$api_key = get_site_option('smsify-api-key');
			$valid_key = false;
			$validationMessage = __("Invalid API Key");
		} else {
			$api_key = get_site_option('smsify-api-key');
			$valid_key = true;
			$validationMessage = __("Your settings have been updated successfully");            
			$response = smsify_get_credits($api_key);
			$credits = json_decode($response['body'])->result;
		}
	} else {
		$api_key = get_site_option('smsify-api-key');
		$smsify_enable_sender_id_override = get_site_option('smsify-enable-sender-id-override');
		if($api_key) {
			$validationMessage = __("Your API Key has been validated successfully.");
			$valid_key = true;
			$response = smsify_get_credits($api_key);
			
			if ( is_wp_error( $response ) ) {
				$validationMessage = $response->get_error_message();
			} else if($response['response']['code'] != 200) {
				$validationMessage = __($response['body']);
			} else {
				$credits = json_decode($response['body'])->result;
			}
		} else {
			$validationMessage = __("Before you start using SMSify, please activate the plugin by pasting your API Key in the text field provided.");            
		}
	}

	require_once 'views/smsify-settings.php';
	
}

add_action( 'show_user_profile', 'smsify_extra_user_profile_fields', 99998 );
add_action( 'edit_user_profile', 'smsify_extra_user_profile_fields', 99998 );
function smsify_extra_user_profile_fields( $user ) {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}    
	$smsify_params = smsify_getConfig();
	wp_enqueue_style('smsify');
	wp_enqueue_style('font-awesome');
	wp_enqueue_style('jquery-ui-1.10.3.custom.min');
	wp_enqueue_script('smsify-sms-controller');
	require_once 'views/smsify-send-user.php';
}

function smsify_schedules() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	$validationMessage = "";
	$smsify_params = smsify_getConfig();
	wp_enqueue_style('smsify');
	wp_enqueue_style('font-awesome');
	wp_enqueue_script('smsify-sms-controller');
	
	$args = array('timeout' => 30, 'sslverify' => false, 'headers' => array('x-smsify-key' => $smsify_params->api_key));
	$response = wp_remote_get($smsify_params->apiEndpoint . "/schedule/list", $args);
	
	if ( is_wp_error( $response ) ) {
		$validationMessage = $response->get_error_message();
	}else if($response['response']['code'] != 200) {
		$validationMessage = __(json_decode($response['body'])->message);
		wp_die( __($validationMessage) );
	} else {
		$response = json_decode($response['body'])->result;
		require_once 'views/smsify-schedules.php';
	}
}

function smsify_reporting() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	$smsify_params = smsify_getConfig();
	$current_year = date('Y');
	if(isset($_GET['year']) && is_numeric($_GET['year']) && strlen($_GET['year']) == 4) {
		$selected_year = $_GET['year'];
	} else {
		$selected_year = $current_year;
	}
	$grandtotal = 0;
	$user_id = 0;
	
	if(isset($_GET['user_id']) && is_numeric($_GET['user_id'])) {
		$user_id = $_GET['user_id'];
	}
	 
	$stats = smsify_get_yearly_stats($selected_year, $user_id);
	$users = smsify_get_users();
	require_once 'views/smsify-reporting.php';
}

function smsify_responses() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	$smsify_params = smsify_getConfig();
	wp_enqueue_style('font-awesome');
	wp_enqueue_script('smsify-sms-controller');
	$args = array('timeout' => 30, 'sslverify' => false, 'headers' => array('x-smsify-key' => $smsify_params->api_key));
	$response = wp_remote_get($smsify_params->apiEndpoint . "/account/data/responses", $args);
	if ( is_wp_error( $response ) ) {
		$validationMessage = $response->get_error_message();
	}else if($response['response']['code'] != 200) {
		$validationMessage = __(json_decode($response['body'])->message);
		wp_die( __($validationMessage) );
	} else {
		$account = json_decode($response['body'])->result;
		$responses = $account->responses;    
		require_once 'views/smsify-responses.php';
	}
	
}
