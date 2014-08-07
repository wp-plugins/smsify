<?php
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
    exit ("Do not access this file directly.");
function smsify_getConfig() {
    global $smsify_params;
    global $current_user;
    $smsify_params = new stdClass();
    $smsify_params->appVersion = '4.1.4';
    $smsify_params->api_key = get_site_option('smsify-api-key');
    $smsify_params->apiprotocol = 'https';
    $smsify_params->apihost = 'www.smsify.com.au';
    $smsify_params->apiEndpoint = $smsify_params->apiprotocol . '://' . $smsify_params->apihost;
    $smsify_params->cssurl = '/' . PLUGINDIR . '/smsify/css';
    $smsify_params->jsurl = '/' . PLUGINDIR . '/smsify/js';
    $smsify_params->imageurl = '/' . PLUGINDIR . '/smsify/images';
    $smsify_params->smsifydir = $_SERVER["DOCUMENT_ROOT"] . '/' . PLUGINDIR . '/smsify';
    
    $smsify_params->messages = array(
                            "send_group_confirmation" => __("You are about to send a group SMS. Would you like to continue?")
    );
    
    wp_register_style('smsify', 
                        $smsify_params->cssurl . '/smsify.css', 
                        array(), 
                        $smsify_params->appVersion,
                        'all');
    wp_register_style('jquery-ui-1.10.3.custom.min', 
                        $smsify_params->cssurl . '/jquery-ui-1.10.3.custom.min.css',
                        array(),
                        $smsify_params->appVersion,
                        'all');
    wp_register_script('smsify-sms-controller', 
                        $smsify_params->jsurl . '/sendsmscontroller.min.js', 
                        array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker'), 
                        $smsify_params->appVersion);
                                                
    return $smsify_params;
}

add_action( 'personal_options_update', 'smsify_update_sms_user_data' );
add_action( 'edit_user_profile_update', 'smsify_update_sms_user_data' );
function smsify_update_sms_user_data( $user_id ) {

    if ( !current_user_can( 'edit_user', $user_id ) ) { return false; }
    update_user_meta( $user_id, 'smsify_mobile', $_POST['smsify_mobile'] );
    update_user_meta( $user_id, 'smsify_message', $_POST['smsify_message'] );
    update_user_meta( $user_id, 'smsify_sender_id', $_POST['smsify_sender_id'] );
}

add_action( 'wp_ajax_smsify_sms_handler', 'smsify_sms_handler' );
function smsify_sms_handler() {
    if ( !current_user_can( 'edit_user', $user_id ) ) { die("Invalid request - 000"); }
    
    if(!isset($_POST['send_to'])) {
        //header("HTTP/1.1 500 Invalid Request");
        die("Invalid request - 002");
    }
    
    if(!isset($_POST['message'])) {
        //header("HTTP/1.1 500 Invalid Request");
        die("Invalid request - 003");
    }
    
    if(!isset($_POST['user_id'])) {
        //header("HTTP/1.1 500 Invalid Request");
        die("Invalid request - 004");
    }
    
    if(!isset($_POST['scheduler'])) {
        //header("HTTP/1.1 500 Invalid Request");
        die("Invalid request - 005");
    }
    
    if(!isset($_POST['schedule_date_time'])) {
        //header("HTTP/1.1 500 Invalid Request");
        die("Invalid request - 006");
    }
    
    $error = false;
    $validationMessage = "";
    $message = $_POST['message'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $mobile = $_POST['send_to'];
    $key = get_site_option('smsify-api-key');
    $method = $_POST['method'];
    $scheduler = $_POST['scheduler'];
    $schedule_date_time = $_POST['schedule_date_time'];
    $returnMessage = new stdClass();
    
    if(strlen($message) > 160) {
        $error = true;
        $validationMessage = __("Your message seems to be longer than 160 characters.");
    }
    
    if(trim($message) == "" ) {
        $error = true;
        $validationMessage = __("Please enter your SMS message");
    }
    
    if(strlen($mobile) < 10) {
        $error = true;
        $validationMessage = __("Your mobile number seems to be invalid.\nPlease correct it and try again.");
    }
    
    if(!is_numeric($mobile)) {
        $error = true;
        $validationMessage = __("Your mobile number seems to be invalid.\nPlease correct it and try again.");
    }
    
    if(isset($_POST['sender_id']) && !is_numeric($_POST['sender_id'])) {
        $error = true;
        $validationMessage = __("Your Sender ID seems to be invalid.\nPlease correct it and try again.");
    }
    
    if($scheduler && $schedule_date_time == "") {
        $error = true;
        $validationMessage = __("If you choose to schedule your SMS, you must specify schedule date and time.");
    }
    
    if(!$error) {    
        $smsify_params = smsify_getConfig();
        $contact = new stdClass();
        $contact->first_name = $first_name;
        $contact->last_name = $last_name;
        $contact->mobile_number = $mobile;
        $args = array('timeout' => 30, 
                        "body" => array(
                        "key"     => $key, 
                        "method"  => $method, 
                        "contacts" => array($contact),
                        "message" => $message,
                        "scheduler" => $scheduler,
                        "schedule_date_time" => $schedule_date_time));

		if(isset($_POST['sender_id'])) {
			$args['body']['sender_id'] = $_POST['sender_id'];
		}

        $result = wp_remote_post($smsify_params->apiEndpoint . "/transport/", $args);
        
        if ( is_wp_error( $result ) ) {
            $validationMessage = $result->get_error_message();
            $returnMessage->status = false;
        }else if($result['response']['code'] != 200) {
            $validationMessage = __($result['body']);
            $returnMessage->status = false;
        } else {
            $result = json_decode($result['body']);
            if($result->status == true) {
                $returnMessage->status = true;
                if($scheduler) {    
                    $validationMessage = __("Your SMS has been scheduled successfully.");
                    $d = date_parse($schedule_date_time);
                    //Update stats for reporting
                    smsify_update_usage(1, $d['year'], $d['month']);
                } else {
                    $validationMessage = __("Your SMS has been queued and will be sent shortly.");
                    //Update stats for reporting
                    smsify_update_usage(1);
                }
                smsify_update_usage(1);
            } else {
                $returnMessage->status = false;
                $validationMessage = __($result->message);
            }
        }
        $returnMessage->message = $validationMessage;
   } else {
       $returnMessage->status = false;
        $returnMessage->message = $validationMessage;
   }
   echo json_encode($returnMessage);
   die();
}

add_action( 'wp_ajax_smsify_sms_group_handler', 'smsify_sms_group_handler' );
function smsify_sms_group_handler() {
    global $wpdb;
    
    if ( !current_user_can( 'edit_user', $user_id ) ) { die("Invalid request - 000"); }
    
    if(!isset($_POST['message'])) {
        //header("HTTP/1.1 500 Invalid Request");
        die("Invalid request - 001");
    }
    
    if(!isset($_POST['tag_id'])) {
        //header("HTTP/1.1 500 Invalid Request");
        die("Invalid request - 002");
    }
    
    if(!isset($_POST['scheduler'])) {
        //header("HTTP/1.1 500 Invalid Request");
        die("Invalid request - 003");
    }
    
    if(!isset($_POST['schedule_date_time'])) {
        //header("HTTP/1.1 500 Invalid Request");
        die("Invalid request - 004");
    }
    
    $error = false;
    $validationMessage = "";
    $message = $_POST['message'];
    $key = get_site_option('smsify-api-key');
    $method = $_POST['method'];
    $tag_id = $_POST['tag_id'];
    $taxonomy = $_POST['taxonomy'];
    $scheduler = $_POST['scheduler'];
    $schedule_date_time = $_POST['schedule_date_time'];
    $returnMessage = new stdClass();
    
    if(strlen($message) > 160) {
        $error = true;
        $validationMessage = __("Your message seems to be longer than 160 characters.");
    }
    
    if(trim($message) == "" ) {
        $error = true;
        $validationMessage = __("Please enter your SMS message");
    }
    
    if($scheduler && $schedule_date_time == "") {
        $error = true;
        $validationMessage = __("If you choose to schedule your SMS, you must specify schedule date and time.");
    }
    
    if(isset($_POST['sender_id']) && !is_numeric($_POST['sender_id'])) {
        $error = true;
        $validationMessage = __("Your Sender ID seems to be invalid.\nPlease correct it and try again.");
    }
    
    if(!$error) {
        $user_ids = get_objects_in_term($tag_id, $taxonomy );
        $args = array('include'=>$user_ids);
        $users = get_users($args);
        foreach($users as $user) {
            if(strlen(trim($user->smsify_mobile))) {    
                $thisUser = new stdClass();
                $thisUser->first_name = $user->first_name;
                $thisUser->last_name = $user->last_name;
                $thisUser->mobile_number = $user->smsify_mobile;
                $contacts[] = $thisUser;
            }
        }
                                   
        $smsify_params = smsify_getConfig();
        $args = array('timeout' => 30, 
                        "body" => array(
                        "key"     => $key, 
                        "method"  => $method, 
                        "contacts" => $contacts,
                        "message" => $message,
                        "scheduler" => $scheduler,
                        "schedule_date_time" => $schedule_date_time));
        
        if(isset($_POST['sender_id'])) {
            $args['body']['sender_id'] = $_POST['sender_id'];
        }
        
        $result = wp_remote_post($smsify_params->apiEndpoint . "/transport/", $args);
        
        if ( is_wp_error( $result ) ) {
            $validationMessage = $result->get_error_message();
            $returnMessage->status = false;
        }else if($result['response']['code'] != 200) {
            $validationMessage = __($result['body']);
            $returnMessage->status = false;
        } else {
            $result = json_decode($result['body']);
            if($result->status == true) {
                $returnMessage->status = true;
                if($scheduler) {    
                    $validationMessage = __("Your SMS has been scheduled successfully.");
                    $d = date_parse($schedule_date_time);
                    //Update stats for reporting
                    smsify_update_usage(count($contacts), $d['year'], $d['month']);
                } else {
                    $validationMessage = __("Your SMS has been queued and will be sent shortly.");
                    //Update stats for reporting
                    smsify_update_usage(count($contacts));
                }
            } else {
                $returnMessage->status = false;
                $validationMessage = __($result->message);
            }
        }
        $returnMessage->message = $validationMessage;
   } else {
       $returnMessage->status = false;
       $returnMessage->message = $validationMessage;
   }
   echo json_encode($returnMessage);
   die();
}

function smsify_reporting_yearly_template($selected_year) {
    $yearly_packet = new stdClass();
    $yearly_packet->$selected_year = new stdClass();
    
    for($i=1; $i <= 12; $i++) {
        $yearly_packet->$selected_year->$i = 0;
    }
    return $yearly_packet;
}

function smsify_get_yearly_stats($year, $user_id=0) {
    global $wpdb;    
    $meta_key = "smsify_" . $year;
    $report = smsify_reporting_yearly_template($year);
    
    $sql = "SELECT u.user_login, um.meta_value, um.user_id FROM " . $wpdb->prefix . "usermeta um 
    INNER JOIN " . $wpdb->prefix . "users u ON (u.ID = um.user_id) 
    WHERE um.meta_key = %s";
    if($user_id) {
        $sql .= " AND u.ID = %d";
        $user_stats = $wpdb->get_results(
                    $wpdb->prepare(
                        $sql, $meta_key, $user_id));
    } else {
        $user_stats = $wpdb->get_results(
                    $wpdb->prepare(
                        $sql, $meta_key));
    }
    
    if(count($user_stats)) {
        //Loop through each user's numbers and add them up
        foreach($user_stats as $user)  {
            $user = json_decode($user->meta_value);
            foreach($user->$year as $month_num => $total) {
               $report->$year->$month_num += $total;
            }            
        }   
    }
    return $report;
}

function smsify_get_yearly_stats_for_user($year, $user_id) {
    $meta_key = 'smsify_'.$year;
    $report = get_user_meta($user_id, $meta_key, true);

    if($report) {
        $report = json_decode($report);
    } else {
        $report = smsify_reporting_yearly_template($year);
    }
    
    return $report;
}

function smsify_update_usage($total, $year=null, $month=null) {
    if(!$year) {
        $year = date('Y');
    }        
    if(!$month) {
        $month = date('n');
    }
    $user_id = get_current_user_id();
    $meta_key = 'smsify_'.$year;
    $stats = get_user_meta($user_id, $meta_key, true);
    
    if($stats) {
        $stats = json_decode($stats);
    } else {
        $stats = smsify_reporting_yearly_template($year);        
    }
    $stats->$year->$month += $total;
    update_user_meta($user_id, $meta_key, json_encode($stats));
}

/**
 * Gets users that have sent SMS to at least one contact
 */
function smsify_get_users() {
    global $wpdb;    
    $meta_key = "smsify_2%"; //should be good for approx another 787 years
    
    $sql = "SELECT u.user_login, u.ID FROM " . $wpdb->prefix . "users u 
    INNER JOIN " . $wpdb->prefix . "usermeta um ON (u.ID = um.user_id) 
    WHERE um.meta_key LIKE %s
    GROUP BY u.ID";
    
    $users = $wpdb->get_results(
                    $wpdb->prepare(
                        $sql, $meta_key));
    return $users;   
}
