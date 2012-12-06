<?php
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
    exit ("Do not access this file directly.");
?>
<div class="wrap columns-2 dd-wrap">
    <div id="icon-upload" class="icon32 icon32-posts-page"><br /></div>
    <h2>SMSify - Video Guides</h2>
    <div id="poststuff" class="metabox-holder has-right-sidebar">
        <?php include('smsify-sidebar.php'); ?>
        <div class="post-body">
            <div class="post-body-content">        
                <div class="stuffbox" style="margin-right:300px;">
                    <h3><label for="link_box">SMSify Video Guides</label></h3>
                    <div class="inside">    
                        <div class="smsify-api-container">
                            <p>SMSify promotional video which demonstrates the benefits of SMS technology and many reasons why you would benefit from using SMSify.</p>
                            <iframe src="http://player.vimeo.com/video/52957980?badge=0" width="500" height="281" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe> <p><a href="http://vimeo.com/52957980">SMSify Promo Video</a> from <a href="http://vimeo.com/user14528592">SMSify</a> on <a href="http://vimeo.com">Vimeo</a>.</p>
                            <br/>
                            <p>This video shows the Trial sign up process and how to locate your API Key once you have signed up. We reccomend viewing in full screen mode.</p>
                            <iframe width="640" height="360" src="http://www.youtube.com/embed/lLH-MHLhOd8?feature=player_detailpage" frameborder="0" allowfullscreen></iframe>
                            <br/>
                            <p>The following video will guide you through all features of SMSify WordPress plugin. We reccomend viewing in full screen mode.</p>
                            <iframe width="640" height="360" src="http://www.youtube.com/embed/DYuoNVsAlY8?feature=player_detailpage" frameborder="0" allowfullscreen></iframe>
                            <p>Key things to remember:<br/>
                                - Add your SMSify API Key<br/>
                                - Create Contact Groups<br/>
                                - Bulk Import users from a csv file, or enter them in manually.<br/>
                                - Click "Save Changes" whenever you add/edit/delete contacts or groups in the Datagrid.<br/>
                                - Click "Cancel Changes" to discard all changes made to the Datagrid.<br/>
                                - Use {first_name} and {last_name} to dynamically insert contact's First Name and Last name. This only works when sending to a Contact Group.<br/>
                                - Schedule your messages. This feature is optional. By default messages will be delivered immediately.<br/></p>
                        </div><!-- end smsify-api-container //-->
                    </div><!-- inside //-->
                 </div><!-- end stuffbox //-->
            </div><!-- end post-body-content //-->
        </div><!-- end post-body //-->
    </div><!-- end poststuff //-->
</div><!-- end wrap columns-2 dd-wrap //-->