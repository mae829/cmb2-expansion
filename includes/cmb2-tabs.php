<?php

class CMB2_Tabs {

	static $instance = false;

	public function __construct() {
		$this->_add_actions();
	}

	/**
	 * Add metabox in User panel to opt out of plugin options
	 */
	public function user_tab_metabox() {

		$cmb2_exp_user	= new_cmb2_box( array(
			'id'			=> 'cmb2-exp-user-box',
			'title'			=> __( 'CMB2 Expansion Options', 'cmb2-exp' ),
			'object_types'	=> array( 'user' ),
		) );

		$cmb2_exp_user->add_field( array(
			'id'		=> 'cmb2-exp-options-title',
			'name'		=> __( 'CMB2 Expansion Options', 'cmb2-exp' ),
			'desc'		=> __( 'Opt-out options for CMB2 expansion plugin.', 'cmb2-exp' ),
			'type'		=> 'title',
			'on_front'	=> false,
		) );

		$cmb2_exp_user->add_field( array(
			'id'		=> 'cmb2-tabs-switch',
			'name'		=> __( 'Tabs', 'cmb2-exp' ),
			'desc'		=> __( 'If box is checked on, tabs WILL NOT be used in Post Types edit pages.', 'cmb2-exp' ),
			'type'		=> 'checkbox',
			'on_front'	=> false
		) );

	}

	/**
	 * Enqueue Tabs Assets
	 */
	public function enqueue_tabs_assets() {
		global $post;

		$post_type_show	= isset($post) && $post->post_type == 'attachment' ? false : true;

		// only enqueue these if CMB2 is present
		if ( wp_style_is( 'cmb2-styles', 'enqueued' ) && wp_script_is( 'cmb2-scripts', 'enqueued' ) && $post_type_show ) {
			wp_enqueue_style( 'cmb2-exp-nav-styles-admin', CMB2_EXP_URL . 'assets/cmb2-exp-tab-nav.admin.css', false, CMB2_EXP_VERSION, false );
			wp_enqueue_script( 'cmb2-exp-nav-scripts-admin', CMB2_EXP_URL . 'assets/cmb2-exp-tab-nav.admin.js', array('jquery'), CMB2_EXP_VERSION, true );
		}

	}

	/**
	 * Meta Tabs
	 *
	 * Called by the "edit_form_after_title" action.
	 *
	 * @return string HTML markup containing the necessary meta tabs.
	 */
	public function meta_tabs() {
		global $post, $wp_meta_boxes;

		if ( $post->post_type == 'attachment' ) {
			return;
		}

		$tabs             = '';
		$screen           = get_current_screen();
		$hidden_mbxs      = get_hidden_meta_boxes( $screen ); // hidden meta boxes in the "screen options" section
		$supports_editor  = post_type_supports($post->post_type, 'editor');

		//for ALL metaboxes that aren't part of the sidebar
		foreach ( $wp_meta_boxes[$post->post_type] as $mbx_location => $mbxs_priority ) {

			// exclude metaboxes on sidebar (normal and advanced only)
			if ( $mbx_location != 'side' ) {

				// priorities: core, default, high, low
				foreach ( $mbxs_priority as $priority => $boxes ) {

					// finally at each box
					foreach ( $boxes as $mbx_name => $box ) {

						// create tabs for all metaboxes, but add class HIDDEN for hidden metaboxes in "screen options"
						$display = ( in_array($mbx_name, $hidden_mbxs) ? ' hidden' : '' );
						$tabs .= sprintf( '<a class="nav-tab %s" href="#%s">%s</a>', $display, $box['id'], $box['title'] ); // ALL THAT FOR THIS!!!!

					} // end foreach boxes

				} // end foreach priorities

			} // end if not 'side' metaboxes

		} // end of ridiculous waterfall

		if ( $tabs != '' ) {
			$post_type_object	= get_post_type_object($post->post_type);
			$post_type_name		= $post_type_object->labels->singular_name;

			$output = '<div id="cmb2-nav">';

				$output .= sprintf('<h2>%s Navigation:</h2>',  ucfirst($post_type_name) );

				$output .= '<div class="nav-tab-wrapper">';

					// only show tab if supports Post Editor
					if ( $supports_editor ) {
						$output .= '<a class="nav-tab" href="#postdivrich">Post Editor</a>';
					}

					$output .= $tabs;

				$output .= '</div>';

			$output .= '</div>';

			echo $output;
		}

	}

	/**
	 * Singleton
	 *
	 * @return A single instance of the current class.
	 */
	public static function singleton() {

		if ( !self::$instance )
		self::$instance = new self();

		return self::$instance;
	}

	/**
	 * Add Actions
	 *
	 * Registers all the WordPress actions and hooks the plugin relies on.
	 */
	private function _add_actions() {

		// Per user option
		add_action( 'cmb2_admin_init', array( $this, 'user_tab_metabox' ), 999 );

		// Only add the tabs if the user didn't opt out of the option
		$user_id	= get_current_user_id();

		if ( get_user_meta( $user_id, 'cmb2-tabs-switch', true ) != 'on' ) {

			// CMB Tab Navigation
			add_action( 'cmb2_footer_enqueue', array( $this, 'enqueue_tabs_assets' ), 10 );
			add_action( 'edit_form_after_title', array( $this, 'meta_tabs' ) );

		}


	}

}
