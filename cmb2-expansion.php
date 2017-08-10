<?php
/**
 * Plugin Name: CMB2 Expansion
 * Description: Expansion plugin for CMB2
 * Version: 1.1
 * Author: Mike Estrada
 * Author URI: https://bleucellar.com/
 */

if ( !defined('ABSPATH') )
	die ( 'YOU SHALL NOT PASS!' );

define( 'CMB2_EXP_DIR', plugin_dir_path(__FILE__) );
define( 'CMB2_EXP_URL', plugin_dir_url(__FILE__) );
define( 'CMB2_EXP_BASE', plugin_basename( __FILE__ ) );
define( 'CMB2_EXP_VERSION', '1.1' );

// check if CMB2 class is defined (possibly use the PRE ACTIVATION hook)
function cmb2_expansion_plugin_init() {
	//check if CMB2 is loaded
	if ( defined( 'CMB2_LOADED' ) ) {

		// include class file
		if ( file_exists( CMB2_EXP_DIR . '/includes/class-cmb2-expansion.php' ) ){

			require_once CMB2_EXP_DIR . '/includes/class-cmb2-expansion.php';

			CMB2_Expansion::singleton();

		}

	} else {

		// IF CMB2 NOT LOADED, SHOW ALERT AND DEACTIVATE
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		deactivate_plugins( CMB2_EXP_BASE );
		add_action( 'admin_notices', 'cmb2_not_active' );

		function cmb2_not_active(){ ?>

			<div class="notice error cgi-cmb2-notice is-dismissible" >
				<p><?php _e( 'CGI CMB2 Expansion depends on CMB2. CMB2 is either not installed or disabled. Therefore, CGI CMB2 Expansion has been disabled', 'cgi-cmb2-exp' ); ?></p>
			</div>

		<?php
		}
	}

}
add_action( 'plugins_loaded', 'cmb2_expansion_plugin_init' );

?>
