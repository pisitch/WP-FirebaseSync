<?php

/*
Plugin Name: WP Firebase Sync
Plugin URI: https://github.com/pisitch/WP-FirebaseSync
Description: Plugin that allow WordPress to sync Post data with Firebase in real time.
Version: 1.1.1
Author: Bawornsit C. 
Original Author: Jared Chu
License: MIT
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/classes/JC_Post.php';

use JC\SimpleCache;
use JCFirebase\JCFirebase;

/**
 * Register menu
 */
if(is_admin())
{
    add_action('admin_menu', 'wfs_add_admin_menu');
    add_action('admin_init', 'wfs_settings_init');
}


function wfs_add_admin_menu()
{
    add_options_page('WP Firebase Sync', 'WP Firebase Sync', 'manage_options', 'wp_firebase_sync', 'wfs_options_page');
}


function wfs_settings_init()
{

    register_setting('pluginPage', 'wfs_settings');
    register_setting('pluginPage2', 'wfs_settings2');

    add_settings_section(
        'wfs_pluginPage_section',
        __('Firebase config', 'wp-firebase-sync'),
        'wfs_settings_section_callback',
        'pluginPage'
    );

    add_settings_section(
        'wfs_pluginPage2_section',
        __('Post Synchronize', 'wp-firebase-sync'),
        'wfs_settings_section2_callback',
        'pluginPage2'
    );

    //page1 fields
    add_settings_field(
        'wfs_firebase_uri',
        __('Firebase URI:', 'wp-firebase-sync'),
        'wfs_text_field_0_render',
        'pluginPage',
        'wfs_pluginPage_section'
    );

    add_settings_field(
        'wfs_firebase_json_key',
        __('Firebase private key in JSON format:', 'wp-firebase-sync'),
        'wfs_text_field_2_render',
        'pluginPage',
        'wfs_pluginPage_section'
    );

    //Page 2 fields
    add_settings_field(
        'wfs_firebase_post_type_taget',
        __('Post type target:', 'wp-firebase-sync'),
        'wfs_listbox_field_0_page2_render',
        'pluginPage2',
        'wfs_pluginPage2_section'
    );

    add_settings_field('wfs_firebase_remove_html_tag',
    __('Remove Html Tags in content (Default:true):', 'wp-firebase-sync'),
    'wfs_checkbox_field_1_page2_render',
    'pluginPage2',
    'wfs_pluginPage2_section');
}


function wfs_text_field_0_render()
{

    $options = get_option('wfs_settings');
    ?>
    <input type='text' name='wfs_settings[wfs_firebase_uri]' value='<?php echo $options['wfs_firebase_uri']; ?>'>
    <?php

}

function wfs_text_field_2_render()
{

    $options = get_option('wfs_settings');
    ?>
    <textarea cols='40' rows='5'
              name='wfs_settings[wfs_firebase_json_key]'><?php echo $options['wfs_firebase_json_key']; ?></textarea>
    <?php

}

function wfs_listbox_field_0_page2_render(){
    $options = get_option('wfs_settings2');
    $isset_post_type_option = isset($options['wfs_firebase_post_type_taget']);
    ?>
    <select name='wfs_settings2[wfs_firebase_post_type_taget]'>
        <option value="pub" <? if($isset_post_type_option){if($options['wfs_firebase_post_type_taget'] == "pub") echo "selected";}else echo "selected";?>>Published post only</option>
        <option value="all" <? if($options['wfs_firebase_post_type_taget'] == "all") echo "selected"; ?>>All post (include draft)</option>
    </select>
    <?
}

function wfs_checkbox_field_1_page2_render(){
    $options = get_option('wfs_settings2');
    $isset_wfs_firebase_remove_html_tag = isset($options['wfs_firebase_remove_html_tag']);
    ?>
        <input type="checkbox" name="wfs_settings2[wfs_firebase_remove_html_tag]" <? if($isset_post_type_option) {echo $options['wfs_firebase_remove_html_tag'];}else{ echo "checked";} ?>>
    <?
}


function wfs_settings_section_callback()
{
    $options = get_option('wfs_settings');

    if (isset($options['wfs_firebase_uri']) && isset($options['wfs_firebase_json_key'])) {
        $firebaseURI = $options['wfs_firebase_uri'];
        $jsonKey = $options['wfs_firebase_json_key'];

        echo "<p>";
        if (empty($firebaseURI) && empty($firebaseEmail) && empty($jsonKey)) {
            echo __('Enter your firebase URI & service account information', 'wp-firebase-sync');
        } else {
            try {
                $firebase = JCFirebase::fromJson($firebaseURI, json_decode($jsonKey));

                if ($firebase->isValid()) {
                    SimpleCache::setEncryptKey($_SERVER['SERVER_SIGNATURE'] . $options['wfs_firebase_json_key']);
                    SimpleCache::add('firebase', $firebase);
                    echo "<span class='notice notice-success'>Firebase connect succeed</span>";
                } else {
                    echo "<span class='notice notice-error'>Firebase connect failed</span>";
                }
            } catch (Exception $exception) {
                $errorMessage = $exception->getMessage();
                echo "<span class='notice notice-error'>$errorMessage</span>";
            }

        }
        echo "</p>";
    }
}

function wfs_settings_section2_callback(){
    $options = get_option('wfs_settings2');

    //====For debug only======
    // if (isset($options['wfs_firebase_post_type_taget'])) {
    //     echo $options['wfs_firebase_post_type_taget'];
    // }
}


function wfs_options_page()
{
    $active_tab = $_GET['tab'] ?? 'init';
    ?>
     <h2>WP Firebase Sync</h2>
        <h2 class="nav-tab-wrapper">
        <a href="?page=wp_firebase_sync&tab=init" class="nav-tab <?php echo $active_tab == 'init' ? 'nav-tab-active': '';?>">Init Config</a>
        <a href="?page=wp_firebase_sync&tab=postsync" class="nav-tab <?php echo $active_tab == 'postsync' ? 'nav-tab-active': '';?>">Post Synchronize</a>
    </h2>
    <form action='options.php' method='post'>
        <?php
        if($active_tab == "init")
        {
            settings_fields('pluginPage');
            do_settings_sections('pluginPage');
            submit_button();
        }else if($active_tab == "postsync")
        {
            settings_fields('pluginPage2');
            do_settings_sections('pluginPage2');
            submit_button();
        }
        ?>

    </form>
    <?php

}

/**
 * Config firebase
 */

function save_post_to_firebase($post_id)
{
    $options = get_option('wfs_settings');
    $options2 = get_option('wfs_settings2');
    SimpleCache::setEncryptKey($_SERVER['SERVER_SIGNATURE'] . $options['wfs_firebase_json_key']);

    if (SimpleCache::exists('firebase')) {
        $firebase = SimpleCache::fetch('firebase', JCFirebase::class);
    } else {
        $firebaseURI = $options['wfs_firebase_uri'];
        $jsonKey = $options['wfs_firebase_json_key'];
        $firebase = JCFirebase::fromJson($firebaseURI, json_decode($jsonKey));

        SimpleCache::add('firebase', $firebase);
    }

    // Cancel if this is just a revision or firebase is not set
    if (is_null($firebase) || wp_is_post_revision($post_id)) {
        return;
    }

    $post = WP_Post::get_instance($post_id);

    if($post->post_type != "post")
    {
        return;
    }

    if(!isset($options2['wfs_firebase_post_type_taget']) || $options2['wfs_firebase_post_type_taget'] == "pub")
    {
        if($post->post_status != "publish"){
            return;
        }
    }

    if ($post->ID) {
        // strip_tags()
        $mapper = new JsonMapper();
        $fbPost = $mapper->map($post, new JC_Post($firebase, $post->ID));

        if(!isset($options2['wfs_firebase_remove_html_tag']) || $options2['wfs_firebase_remove_html_tag']){
            $fbPost->post_content = trim(strip_tags($fbPost->post_content));
        }

        $fbPost->save();
    }
}

add_action('save_post', 'save_post_to_firebase');