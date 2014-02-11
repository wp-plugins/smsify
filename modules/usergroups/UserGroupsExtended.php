<?php
/*
Plugin Name: SMSify
Description: Send SMS to User Groups
Version: 1.0.0
Author: SMSify
Author URI: http://www.smsify.com.au
*/
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
    exit ("Do not access this file directly.");

require_once 'UserGroups.php';
add_action('plugins_loaded', array('SMSify_User_Groups', 'load'), 200);

class SMSify_User_Groups extends KWS_User_Groups {
    function load() {
        $SMSify_User_Groups = new SMSify_User_Groups();
    }
    
    function __construct() {
        add_action('admin_init', array(&$this, 'remove_add_form_actions'), 1001);
        parent::__construct();
    }
    
    // Get rid of theme, plugin crap for other taxonomies.
    function remove_add_form_actions($taxonomy) {
        remove_all_actions('after-user-group-table');
        remove_all_actions('user-group_edit_form');
        remove_all_actions('user-group_add_form_fields');

        // If you use Rich Text tags, go ahead!
        if(function_exists('kws_rich_text_tags')) {
            add_action('user-group_edit_form_fields', 'kws_add_form');
            add_action('user-group_add_form_fields', 'kws_add_form');
        }

        add_action('user-group_add_form_fields', array(&$this, 'add_form_color_field'), 10, 2);
        add_action('user-group_edit_form', array(&$this, 'add_form_color_field'), 10, 2);
        add_action('user-group_add_form_fields', array(&$this, 'smsify_group_sms'), 11, 2);
        add_action('user-group_edit_form', array(&$this, 'smsify_group_sms'), 11, 2);
    }
    
    function smsify_group_sms($tag, $taxonomy = '') {
        $tegid=$_REQUEST['tag_ID'];
        if($tegid) {
            $smsify_params = smsify_getConfig();
            wp_enqueue_style('smsify');
            wp_enqueue_style('jquery-ui-1.10.3.custom.min');
            wp_enqueue_script('smsify-sms-controller');
            require_once WP_PLUGIN_DIR . '/smsify/views/smsify-send-group.php';
        }
    }
}
    