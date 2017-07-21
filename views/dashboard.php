<?php
if (!defined('ABSPATH')) {exit;}
?>

<h1>Firebase FCM</h1>
<div class="fcm_form">
<form action="options.php" method="post">
    <?php settings_fields( 'fcm_group'); ?>
    <?php do_settings_sections( 'fcm_group' ); ?>
<table>
    <tbody>

    <tr  height="70">
        <td><label for="fcm_api">FCM Server Key</label> </td>
        <td><input id="fcm_api" name="fcm_server_key" type="text" value="<?php echo get_option( 'fcm_server_key' ); ?>" required="required" /></td>
    </tr>


    <tr  height="70">
        <td><label for="fcm_title">FCM Title</label> </td>
        <td><input id="fcm_title" placeholder="Name of Title" name="fcm_title" type="text" value="<?php echo get_option( 'fcm_title' );  ?>" required="required" /></td>
    </tr>
    <tr  height="70">
        <td><label for="fcm_topic">FCM Topic</label> </td>
        <td><input id="fcm_topic" placeholder="Name of Topic setup in application" name="fcm_topic" type="text" value="<?php echo get_option( 'fcm_topic' );  ?>" required="required" /></td>
    </tr>

    <tr  height="70">
        <td><label for="post_enable">Enable Save</label> </td>
        <td><input id="post_enable" name="fcm_post_enable" type="checkbox" value="1" <?php checked( '1', get_option( 'fcm_post_enable' ,"1") ); ?>  /></td>
    </tr>

    <tr  height="70">
        <td><label for="page_enable">Enable Update</label> </td>
        <td><input id="page_enable" name="fcm_page_enable" type="checkbox" value="1" <?php checked( '1', get_option( 'fcm_page_enable' ,"1") ); ?>  /></td>
    </tr>


    </tbody>
    </table>
    <div class="col-sm-10">
        <input type="submit" name="submit" value="Update"/>
    </div>
</form>
</div>
<hr>
<div class="fcm_form">
<form action="#" method="post" id="send_notification">
    <input type="hidden" name="action" value="send_fcm">
    <p>FCM Topic</p>
    <input type="text" name="topics" value="<?php echo get_option( 'fcm_topic' );  ?>">
    <p>Notification Title</p>
    <input type="text" name="title" value="">
    <p>Notification Content</p>
    <textarea name="content" id="content" rows="5" cols="100" ></textarea>

    <br>
    <input type="submit" value="Submit">
</form>
</div>