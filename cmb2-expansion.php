<?php
/**
  * Plugin Name: CMB2 Expansion
  * Description: Expansion plugin for CMB2
  * Version: 1.0
  * Author: Mike Estrada
  */

if ( !defined('ABSPATH') )
	die ( 'YOU SHALL NOT PASS!' );

define( 'CMB2_EXP_DIR', plugin_dir_path(__FILE__) );
define( 'CMB2_EXP_URL', plugin_dir_url(__FILE__) );
define( 'CMB2_EXP_BASE', plugin_basename( __FILE__ ) );
define( 'CMB2_EXP_VERSION', '1.0' );

// check if CMB2 class is defined (possibly use the PRE ACTIVATION hook)
function cmb2_exp_expansion_plugin_init() {
  //check if CMB2 is loaded
  if( defined( 'CMB2_LOADED' ) ) {

	// add functionality into cmb2_show_on
	if ( file_exists( CMB2_EXP_DIR . '/includes/show-on-support.php' ) ) {
	  require_once( CMB2_EXP_DIR . '/includes/show-on-support.php' );
	}

	// add 'repeat_limit' option for repeatable fields/groups
	if ( file_exists( CMB2_EXP_DIR . '/includes/repeat-limit.php' ) ) {
	  require_once( CMB2_EXP_DIR . '/includes/repeat-limit.php' );
	}

	// add tab navigation for meta boxes
	if ( file_exists( CMB2_EXP_DIR . '/includes/tab-navigation.php' ) ) {
	  require_once( CMB2_EXP_DIR . '/includes/tab-navigation.php' );
	}

	// add custom scripts/styles
	add_action( 'admin_print_scripts-post-new.php', 'cmb2_exp_scripts' );
	add_action( 'admin_print_scripts-post.php', 'cmb2_exp_scripts' );
	function cmb2_exp_scripts() {
		wp_enqueue_script( 'cmb2-exp-scripts-admin', CMB2_EXP_URL . 'assets/cmb2-exp.admin.js', array('jquery'), false );
	}

  }
  //IF CMB2 NOT LOADED, SHOW ALERT
}
add_action( 'plugins_loaded', 'cmb2_exp_expansion_plugin_init' );

?>
