<?php
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
    exit ("Do not access this file directly.");
wp_enqueue_style('kendo-common');
wp_enqueue_style('kendo-default');
wp_enqueue_style('smsify');
wp_enqueue_script('settings-controller');

$credits = 0;
if(smsify_ping()) {
    $credits = smsify_checkCredits();
}
$groups = array();
if($credits) {
    $groups = json_decode(trim(file_get_contents($params->apiEndpoint . '/transport/?method=getGroups&key=' . $params->api_key)));
}

if($credits) {
    echo '<script>var existing_app_user = true;</script>';
} else {
    echo '<script>var existing_app_user = false;</script>';
}

$errorMessage = "";
$successMessage = "";
if (!extension_loaded('curl')) {
    echo("<div class='error smsify-error'><br/>We seem to have a little problem. CURL PHP module doesn't seem to be installed on your web server. Please install it and reload this page.</div>");
}

if(isset($_FILES['csvfile']['tmp_name'])) {
    if($_FILES['csvfile']["size"] > 10240000) {
        die("File too large");   
    }
    $ch = curl_init();
    if (!$ch) {
        die("Couldn't initialize a cURL handle");
    }
    $url = $params->apiEndpoint . '/transport/';
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    $post = array(
        "csvfile"=>"@".$_FILES['csvfile']['tmp_name'],
        "key"=>$params->api_key,
        "group_id"=>$_POST['group_id'],
        "method"=>"uploadContacts"
    );
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post); 
    $response = curl_exec($ch);
    if(!curl_errno($ch)) {
        $response = json_decode($response);
        if($response->status) {    
            $successMessage = "Hurray! Your contacts have been imported successfully. <a href='admin.php?page=wp-smsify-app'>Start SMSifying!</a>";
        } else {
            $errorMessage = $response->message;
        }  
    } else {
        $errorMessage = "There was a problem importing your contacts. Make sure you select a CSV file to upload and have CURL PHP module installed on your web server.";
    }
    curl_close ($ch);
}
?>
<div id="smsifywindow2"></div>
<div class="wrap columns-2 dd-wrap">
    <div id="icon-users" class="icon32 icon32-posts-page"><br /></div>
    <h2>SMSify - Import Users</h2>
    <?php if(!$credits && $_SERVER['SERVER_NAME'] != $params->apihost) smsify_show_error() ?>
    <div id="poststuff" class="metabox-holder has-right-sidebar">
        <?php include('smsify-sidebar.php'); ?>
        <div class="post-body"<?php if(!$credits) echo ' style="display:none;"' ?>>
            <div class="post-body-content">        
                <div class="stuffbox" style="margin-right:300px;">
                    <h3><label for="link_box">Import Users</label></h3>
                    <div class="inside">    
                        <div class="smsify-api-container">
                            <?php if(strlen($errorMessage)) : ?>
                                <div class="smsify-error"><?php echo $errorMessage; ?></div>
                            <?php endif ?>
                            <?php if(strlen($successMessage)) : ?>
                                <div class="smsify-success"><?php echo $successMessage; ?></div>
                            <?php endif ?>
                            <form name="smsify-import" method="POST" enctype="multipart/form-data">    
                                <div class="smsify-formrow">
                                    <label for="group_id">Select Group: <select name="group_id" id="group_id">
                                        <?php foreach ($groups as $group) {
                                           echo '<option value="' . $group->group_id . '">' . $group->group_name . '</option>'; 
                                        }?>   
                                    </select></label>
                                </div>
                                <div class="smsify-formrow">
                                    <label for="smsify-import">Select File: <input type="file" name="csvfile" id="csvfile" data-url="<?php echo $params->apiEndpoint ?>/importContacts.php"> (Max 10MB)</label>
                                </div>
                                <div class="smsify-formrow">
                                    <input id="import" type="submit" name="import" class="button-primary" value="IMPORT"/>
                                </div>
                            </form>
                            <p>File must be in CSV format.  <a href="http://smsify.s3.amazonaws.com/wp-content/uploads/2012/05/csv_sample.csv" title="Sample CSV format">Download a sample CSV file</a>.</p>
                        </div><!-- end smsify-api-container //-->
                    </div><!-- inside //-->
                 </div><!-- end stuffbox //-->
            </div><!-- end post-body-content //-->
        </div><!-- end post-body //-->
    </div><!-- end poststuff //-->
</div><!-- end wrap columns-2 dd-wrap //-->