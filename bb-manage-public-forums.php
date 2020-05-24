<?php
/*
Plugin Name: BuddyBoss manage public forums
Plugin URI:  https://wordpress.org/plugins/buddyboss-manage-public-forums/
Description: Allow users to manage public forums & sub-forums.
Version:     1.0.0
Author:      John Albert Catama
Author URI:  https://github.com/jcatama
License:     GPL2
 
BuddyBoss manage public forums is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
BuddyBoss manage public forums is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with BuddyBoss manage public forums. If not, see https://github.com/jcatama/buddyboss-manage-public-forums/blob/master/LICENSE.md.
*/

register_activation_hook( __FILE__, 'bb_manage_public_forums_activate' );
function bb_manage_public_forums_activate() {
  $plugin = plugin_basename( __FILE__ );
  if(!is_plugin_active('buddyboss-platform/bp-loader.php') and current_user_can('activate_plugins')) {
    wp_die('Sorry, but this plugin requires the BuddyBoss Platform Plugin to be installed and active. <br><a href="' . admin_url( 'plugins.php' ) . '">&laquo; Return to Plugins</a>');
    deactivate_plugins($plugin);
  }
}

add_action( 'wp_enqueue_scripts', 'bb_manage_public_forums_scripts_styles', 9999 );
function bb_manage_public_forums_scripts_styles() {
  wp_enqueue_script( 'bb-mpf-css', plugins_url('/lib/main.js', __FILE__), array('jquery'), '1.0.0' );
  wp_enqueue_style( 'bb-mpf-js', plugins_url('/lib/main.css', __FILE__), false, '1.0.0' );
}

/*
* Core functions
*/

add_action( 'bp_setup_nav', 'bb_manage_public_forums_tab', 1000 );
function bb_manage_public_forums_tab() {
  global $bp;
  bp_core_new_nav_item(array( 
    'name' => __( 'Manage Forums', 'bbmpf' ), 
    'slug' => 'manage-forums', 
    'position' => 100,
    'screen_function' => 'bb_manage_public_forums',
    'show_for_displayed_user' => true,
    'item_css_id' => 'bb_manage_public_forums'
  ));
}

function bb_manage_public_forums () {
  add_action( 'bp_template_title', 'bb_manage_public_forums_title' );
  add_action( 'bp_template_content', 'bb_manage_public_forums_content' );
  bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function bb_manage_public_forums_title() {
  _e( 'Manage Forums', 'bbmpf' );
}

function bb_manage_public_forums_content() {
  include_once('bb-manage-public-forums-display.php');
}

/*
* AJAX core
*/
add_action('wp_enqueue_scripts' , function(){
  wp_localize_script('jquery', 'bbmpfapi', array('ajaxurl' => admin_url('admin-ajax.php')));
});

add_action('wp_ajax_bbmpf_subscribe_to_forum', 'bbmpf_subscribe_to_forum');
add_action('wp_ajax_nopriv_bbmpf_subscribe_to_forum', 'bbmpf_subscribe_to_forum_null');
function bbmpf_subscribe_to_forum() {
  try {
    $user_id = get_current_user_id();
    $forum_ids = explode(',', $_POST['subs_forum_ids']);
    foreach($forum_ids as $fid) {
      if(is_numeric($fid)) {
        bbp_add_user_forum_subscription($user_id, $fid);
      }
    }
  
    $unforum_ids = explode(',', $_POST['unsubs_forum_ids']);
    foreach($unforum_ids as $fid) {
      if(is_numeric($fid)) {
        bbp_remove_user_forum_subscription($user_id, $fid);
      }
    }
  } catch(Exception $e) {
    echo json_encode(['status' => false, 'message' => $e->getMessage()]);
    exit;
  }

  echo json_encode(['status' => true]);
  exit;
}

function bbmpf_subscribe_to_forum_null() {
  echo json_encode(['status' => false, 'message' => 'transaction not allowed']);
  exit;
}

/*
* Misc functions
*/

function bbmpf_get_forum_query($id) {
  return get_pages(
    [
      'post_type' => bbp_get_forum_post_type(),
      'numberposts' => -1,
      'post_status' => ['publish'],
      'parent' => $id
    ]
  );
}

function bbmpf_check_if_subs($subs_forum, $forum_id) {
  if(in_array($forum_id, $subs_forum)) {
    return 'checked';
  }
  return '';
}