<?php
/**
 *
 * Copyright: © 2012
 * {@link http://www.smsify.com.au/ SMSify.}
 * ( coded in the Australia )
 *
 * Released under the terms of the GNU General Public License.
 * You should have received a copy of the GNU General Public License,
 * along with this software. In the main directory, see: /licensing/
 * If not, see: {@link http://www.gnu.org/licenses/}. 
 *
 * @package SMSify
 * @version 3.0.1
 */
/*
Plugin Name: SMSify
Plugin URI: http://www.smsify.com.au/smsify-wordpress-plugin/
Description: SMSify is a free WordPress plugin that allows you to <strong>send personalised SMS messages</strong> to a large number of contacts very quickly. You can also <strong>import contacts</strong> from a csv file and <strong>schedule messages</strong>.  Beautiful user interface and very simple to use. Screenshots available.
Author: SMSify
Version: 3.0.1
Author URI: http://www.smsify.com.au/
*/
if(realpath(__FILE__) === realpath($_SERVER["SCRIPT_FILENAME"]))
	exit("Do not access this file directly.");
	
require'includes/functions.php';
add_action( 'admin_menu', 'smsify_menu' );
function smsify_menu() {
	$page = add_menu_page( 'SMSify - bulk SMS delivery', 'SMSify', 'manage_options', 'wp-smsify-videoguides', null, '/wp-content/plugins/smsify/images/favicon.ico' );
	$page_videoguides = add_submenu_page( 'wp-smsify-videoguides', 'SMSify Video Guides', 'Video Guides', 'manage_options', 'wp-smsify-videoguides', 'smsify_plugin_videoguides');
	$page_setup = add_submenu_page( 'wp-smsify-videoguides', 'Settings', 'Settings', 'manage_options', 'wp-smsify-settings', 'smsify_plugin_settings');
	$page_app = add_submenu_page( 'wp-smsify-videoguides', 'SMSify', 'Send SMS', 'manage_options', 'wp-smsify-app', 'smsify_plugin_app');
    $page_import = add_submenu_page( 'wp-smsify-videoguides', 'SMSify', 'Bulk Import', 'manage_options', 'wp-smsify-import', 'smsify_bulk_import');
    	
}
function smsify_plugin_videoguides() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	$params = smsify_getConfig();
    require 'views/smsify-videoguides.php';
	
}
function smsify_plugin_settings() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	$params = smsify_getConfig();
    require 'views/smsify-settings.php';
	
}
function smsify_plugin_app() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	$params = smsify_getConfig();
	require 'views/smsify-app.php';
}
function smsify_bulk_import() {
    if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    $params = smsify_getConfig();
    require 'views/smsify-import.php';
}
?>
