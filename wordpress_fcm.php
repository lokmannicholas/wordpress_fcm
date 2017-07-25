<?php
/*
Plugin Name:Wordpress fcm
Description:Use Wordpress to send  Notification to mobile vias Firebase
Version:1.1
Author:Lokmannicholas
Author URI:https://github.com/lokmannicholas
License:GPL2
License URI:https://www.gnu.org/licenses/gpl-2.0.html
*/
if (!defined('ABSPATH')) {
    exit;
}



Class FCM
{

    public function __construct()
    {
        // Installation and uninstallation hooks
        register_activation_hook(__FILE__, array($this, fcm_activate));
        register_deactivation_hook(__FILE__, array($this, fcm_deactivate));
        add_action('admin_menu', array($this, fcm_setup_admin_menu));
        add_action("admin_init", array($this, fcm_js_scripts));
        add_action("admin_init", array($this, fcm_css_scripts));
        add_action('admin_init', array($this, fcm_settings));
        add_action('save_post', array($this, fcm_on_post_save),10, 3);
	    add_action('wp_ajax_send_fcm', array($this, fcm_custom_notification));
    }

    public function fcm_setup_admin_menu()
    {
	    add_menu_page( 'Firebase Menu ', 'Firebase FCM', 'manage_options', plugin_dir_path( __DIR__ )."wordpress_fcm/views/dashboard.php", '', 'dashicons-welcome-widgets-menus', 90 );
    }

    public function fcm_admin_page()
    {
        include(plugin_dir_path(__FILE__) . 'views/dashboard.php');
    }

    function fcm_js_scripts()
    {
        wp_enqueue_script("jquery");
        wp_enqueue_script("fcm.js", FCM_URL . "assets/js/fcm.js");
    }

    function fcm_css_scripts()
    {
        wp_enqueue_style("fcm.css", FCM_URL . "assets/css/fcm.css");
    }

    public function fcm_activate()
    {

    }

    public function fcm_deactivate()
    {
    }


    function fcm_settings()
    {    //register our settings
        register_setting('fcm_group', 'fcm_server_key');
        register_setting('fcm_group', 'fcm_topic');
	    register_setting('fcm_group', 'fcm_title');
        register_setting('fcm_group', 'fcm_post_enable');
	    register_setting('fcm_group', 'fcm_page_enable');

    }


    function fcm_on_post_save($post_id, $post, $update) {

	$content = get_the_title($post_id);
        if(get_option('fcm_server_key') && get_option('fcm_topic')) {
            //new post/page
            if (get_post_status($post_id)) {
                    if (get_post_status($post_id) == 'publish') {
                        if (get_post_type($post_id) == 'post' && (get_option('fcm_post_enable') == 1 )) {
                            $this->fcm_send_notification($content);

                        } elseif (get_post_type($post_id) == 'page'  && ( get_option('fcm_page_enable') == 1  )) {
                            $this->fcm_send_notification($content);
                        }


                    }

            }
        }

    }

    function fcm_custom_notification(){


	    $result = $this->fcm_notification($_REQUEST['content'],$_REQUEST['title'],$_REQUEST['topics']);


	    header( "Content-Type: application/json" );
	    echo json_encode($result);
	    die();


    }


	function fcm_send_notification($content){

		$result = $this->fcm_notification($content,get_option('fcm_title'));

		echo '<div class="row">';
		echo '<div><h2>Debug Information</h2>';

		echo '<pre>';

		echo '</pre>';

		echo '<p><a href="'. admin_url('admin.php').'?page=test_notification">Retry</a></p>';
		echo '<p><a href="'. admin_url('admin.php').'?page=fcm_slug">Home</a></p>';

		echo '</div>';
	}


    function fcm_notification($content,$title = null , $topics = null){

        $topics =  $topics==null?"'".get_option('fcm_topic')."' in topics":"'".$topics."' in topics";
        $apiKey = get_option('fcm_server_key');
        $url = 'https://fcm.googleapis.com/fcm/send';
        $headers = array(
            'Authorization: key=' . $apiKey,
            'Content-Type: application/json'
        );
        $notification_data = array(    //// when application open then post field 'data' parameter work so 'message' and 'body' key should have same text or value
            'message'           => $content
        );

        $notification = array(       //// when application close then post field 'notification' parameter work
            'body'  => $content,
            'title' => $title
        );

        $post = array(
            'condition'         => $topics,
            'notification'      => $notification,
            "content_available" => true,
            'priority'          => 'high',
            'data'              => $notification_data
        );
        //echo '<pre>';

        // Initialize curl handle
        $ch = curl_init();

        // Set URL to GCM endpoint
        curl_setopt($ch, CURLOPT_URL, $url);

        // Set request method to POST
        curl_setopt($ch, CURLOPT_POST, true);

        // Set our custom headers
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Get the response back as string instead of printing it
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Set JSON post data
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));

        // Actually send the push
        $result = curl_exec($ch);

        // Close curl handle
        curl_close($ch);

        // Debug GCM response

        $result_de = json_decode($result);

        return $result;

        //var_dump($result); die;

    }


}

$mFCM = new FCM();

