<?php
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
    exit ("Do not access this file directly.");
?>
<h2><?php _e("SMSify Responses"); ?></h2>
<table class="wp-list-table widefat fixed">
    <thead>
        <tr>
            <th scope="col"><?php _e("From"); ?></th>
            <th scope="col"><?php _e("Date (UTC)"); ?></th>
            <th scope="col"><?php _e("Message"); ?></th>            
        </tr>
    </thead>
    <tbody>
        <?php foreach ($responses as $response) : ?> 
            <tr>
                <td><?php echo $response->from;  ?></td>
                <td><?php echo $response->date_sent;  ?></td>
                <td><?php echo $response->body;  ?></td>
            </tr>
       <?php endforeach ?>
    </tbody>
    <tfoot>
        <tr>
            <th scope="col"><?php _e("From"); ?></th>
            <th scope="col"><?php _e("Date (UTC)"); ?></th>
            <th scope="col"><?php _e("Message"); ?></th>            
        </tr>
    </tfoot>
</table>