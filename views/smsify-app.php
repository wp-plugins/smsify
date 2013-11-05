<?php if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
    exit ("Do not access this file directly.");

$credits = 0;
if(smsify_ping()) {
    $credits = smsify_checkCredits();
}

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
<div id="smsifywindow2"></div>
<div id="icon-edit-comments" class="icon32"><br /></div>
<h2 class="smsify-app-title">Send SMS</h2>
<!-- CONTENT (start) -->
<?php if(!$credits && $_SERVER['SERVER_NAME'] != $params->apihost) smsify_show_error() ?>    
<div id="content" role="main"<?php if(!$credits) echo ' style="display:none;"' ?>>
    <div id="smsify-loading"><img src="<?php echo $params->cdnurl ?>/css/kendo/Default/loading-image.gif" alt="loading..." /><p>Loading SMSify</p></div>
    <div class="inner k-content smsify-main-app" style="display:none;">
        <ul id="smsifymenu">
            <li class="item-0">My Contacts</li>
            <li class="item-10">Contact Groups</li>
            <li class="item-20">Send SMS</li>
            <li class="item-30">Activity Charts</li>
            <li class="item-40">Import Contacts</li>
            <li class="item-50">My Schedules</li>
        </ul>
        <div class="smsify-content">
            <div class="content-item-0">
                <div class="k-block k-shadow">   
                    <strong>Remember to click "Save Changes" button and wait for a few seconds to apply your changes!</strong> 
                    <div id="contactsgrid"></div>
                    <div id="quicksendPopup"></div>
                </div>
            </div>
            <div class="content-item-10">
                <div class="k-block k-shadow">    
                    <p>This page allows you to manage your contact groups. Your 'Default' contact group is not listed here because it cannot be deleted or modified. The grid below will only show the groups you created yourself.</p>
                    <p>When you create, update or delete group(s), press <strong>Save changes</strong> button to save your changes permanently.</p>
                    <p><strong>Take caution when deleting groups. If you delete a group you will also delete all contacts associated with that group.</strong></p>
                    <div id="groupsgrid"></div>
                </div>
            </div>
            <div class="content-item-20">
                <div id="sms-group-container" class="k-block k-shadow">        
                    <span class="smsify-credits"></span>
                    <div class="sendToGroupSuccess k-block k-success-colored"><p>Your message has been added to the delivery queue successfully.<br/>Your credit(s) will be deducted after SMS has been sent successfully.</p></div>
                    <form name="sendSMStoGroup" id="sendSMStoGroup" method="post">    
                        <div id="sendToGroupContainer">
                            <ul id="fieldlist">
                                <li><label for="selectSMSGroup">Select a Group to send your messages to:</label><input name="selectSMSGroup" id="selectSMSGroup" value="" required="required" /></li>
                                <li>&nbsp;</li>
                                <li><label for="smsEditor">Message (max 160 characters) *</label><textarea name="smsEditor" id="smsEditor" rows="3" cols="7" maxlength="160" required="required"></textarea><br/><span class="k-invalid-msg" data-for="smsEditor"></span></li>
                            </ul>
                        </div>
                        <div id="schedule-container">
                            <label for="schedule">Schedule: </label><input type="checkbox" id="schedule" data-bind="checked: scheduled" /><br/> 
                            <label for="scheduleDatePicker">Select date:</label><input id="scheduleDatePicker" data-bind="enabled: scheduled"/><br/>
                            <label for="scheduleTimePicker">Select time:</label><input id="scheduleTimePicker" data-bind="enabled: scheduled"/><br/>
                            <label for="run_every">Run:</label><select id="run_every" data-bind="enabled: scheduled">
                                <option value="0">once</option>
                                <option value="86400">every day</option>
                                <option value="604800">every week</option>
                                <option value="1209600">every 2 weeks</option>
                                <option value="1814400">every 3 weeks</option>
                                <option value="2592000">every month</option>
                                <option value="5184000">every 2 months</option>
                                <option value="7776000">every 3 months</option>
                                <option value="15552000">every 6 months</option>
                                <option value="31104000">every year</option>
                            </select><br/>
                            <label for="run_times">Run times (0 = forever):</label><input id="run_times" type="number" value="1" min="0" max="365" step="1" data-bind="enabled: scheduled" /><br/>
                            <br/>
                            <p>Your local timezone is detected automatically and is used to deliver messages.</p>
                            <input type="submit" name="btn_send_to_group" id="btn_send_to_group" class="button-primary" value="SEND" /><span class="sendToGroupProgress"><img src="<?php echo $params->cdnurl ?>/css/kendo/Default/loading-image.gif" alt="loading..." /><p>Sending...</p></span>
                        </div>
                    </form>
                </div>
            </div>
            <div class="content-item-30">
                <br/>
                <div id="activitychart" class="k-content absConf">
                    <div class="chart-wrapper">
                        <div id="barchart"></div>
                    </div>
               </div>
                
               <div id="dailychart" class="k-content absConf">
                   <div class="chart-wrapper">
                       <div id="dailychart"></div>
                   </div>
               </div>
            </div>
            <div class="content-item-40">
                <div id="uploadContactsWrapper" class="k-block k-shadow">
                <form id="uploadform" method="post">
                    <ul>    
                        <li><label for="uploadGroup">Upload contacts to this group:</label><br/><input id="uploadGroup" value="0" /></li>
                        <li>                                   
                            <label for="csvfile">Select or drag a csv file to import (10MB limit): </label>
                            <input type="file" id="csvfile" name="csvfile" /> 
                        </li>    
                    </ul>
                </form>
                <p>File must be in CSV format. Column values are read in the following order: Mobile, First Name, Last Name, Email.  <a href="//d2c8ezxpvufza0.cloudfront.net/wp-content/uploads/2012/05/csv_sample.csv" title="Sample CSV format">You can download a sample CSV file here</a>.</p>
                </div>
            </div>
            <div class="content-item-50">
                <div id="myschedules" class="k-content absConf">
                    <div class="k-block k-shadow">    
                        <strong>Remember to click "Save Changes" button and wait for a few seconds to apply your changes!</strong>
                        <div id="schedulesgrid"></div>
                    </div>
               </div>
            </div>
        </div><!-- .smsify-content (end) -->
    </div><!-- inner (end) -->
</div><!-- #content (end) -->
<script type="text/x-kendo-template" id="smsTemplate">
    <div id="quicksendPopup" style="margin-right:10px;">
        <div class="quicksendSuccess k-block k-success-colored"><p>Your message has been added to the delivery queue successfully.<br/>Your credit(s) will be deducted after SMS has been sent successfully.</a></div>
        <div id="quicksendContainer">   
            <strong>#= first_name # #= last_name # - <em>#= mobile_number #</em></strong>
            <br/><br/>
            <input type="hidden" name="quicksend_contact_id" id="quicksend_contact_id" value="#= contact_id #" />
            <label for="quicksendEditor" style="width:250px;">Message (max 160 characters) *</label><textarea name="quicksendEditor" id="quicksendEditor" rows="5" maxlength="160" style="width:290px;"></textarea>
            <div style="height:20px;"></div>
            <div class="k-block k-shadow" id="schedule-container-quick">
                <label for="schedule-quick">Schedule: </label><input type="checkbox" id="schedule-quick" data-bind="checked: scheduled" /><br/> 
                <label for="scheduleDatePicker-quick">Select date:</label><input id="scheduleDatePicker-quick" data-bind="enabled: scheduled" style="width:150px;"/><br/>
                <label for="scheduleTimePicker-quick">Select time:</label><input id="scheduleTimePicker-quick" data-bind="enabled: scheduled" style="width:150px;"/><br/>
                <label for="run_every-quick">Run:</label><select id="run_every-quick" data-bind="enabled: scheduled" style="width:150px;">
                                <option value="0">once</option>
                                <option value="86400">every day</option>
                                <option value="604800">every week</option>
                                <option value="1209600">every 2 weeks</option>
                                <option value="1814400">every 3 weeks</option>
                                <option value="2592000">every month</option>
                                <option value="5184000">every 2 months</option>
                                <option value="7776000">every 3 months</option>
                                <option value="15552000">every 6 months</option>
                                <option value="31104000">every year</option>
                            </select><br/>
                <label for="run_times-quick">Run times (0 = forever):</label><input id="run_times-quick" type="number" value="1" min="0" max="365" step="1" data-bind="enabled: scheduled" style="width:150px;" /><br/>
                <br/>
                <p>Your local timezone is used to deliver messages.</p>
                <p align="right"><button class="k-button btn_send_to_number" id="btn_send_to_number">SEND</button></p>
            </div>
        </div>
        <div id="quicksendProgress"><img src="<?php echo $params->cdnurl ?>/css/kendo/Default/loading-image.gif" alt="loading…" /><p>Sending…<br/>Please wait</p></div>            
    </div>
</script>
<!-- CONTENT (end) -->