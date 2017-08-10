<?php
/*
	Add tab navigation of metaboxes
*/

	// add tab navigation to make Metaboxes easier to deal with for users
	// add_action( 'edit_page_form', 'meta_tabs' );
	// add_action( 'edit_form_advanced', 'meta_tabs' );
	// add_action( 'edit_form_after_editor', 'meta_tabs' ); // adding tabs after the post editor
	add_action( 'edit_form_after_title', 'meta_tabs' ); // adding tabs after title/permalink
	// add_action( 'edit_form_top', 'meta_tabs' ); // adding tabs before title/permalink
	function meta_tabs() {
		global $post, $wp_meta_boxes;

		$tabs             = '';
		$screen           = get_current_screen();
		$hidden_mbxs      = get_hidden_meta_boxes( $screen ); // hidden meta boxes in the "screen options" section
		$supports_editor  = post_type_supports( $post->post_type, 'editor' );

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

				$output .= sprintf('<h2>%s Navigation:</h2>',  ucfirst( $post_type_name ) );

				$output .= '<div class="nav-tab-wrapper">';

					// only show tab if supports Post Editor
					if( $supports_editor ) {
						$output .= '<a class="nav-tab" href="#postdivrich">Post Editor</a>';
					}

					$output .= $tabs;

				$output .= '</div>';

			$output .= '</div>';

			echo $output;
		}

	}//end function meta_tabs()

	//add custom styling and js to admin sections
	function tabs_enqueue( $hook ) {
		$pages = ['post-new.php', 'post.php'];
		if ( !in_array($hook, $pages) ) {
				return;
		}
		wp_enqueue_style( 'tab-nav-styles-admin', CMB2_EXP_URL . 'assets/tab-navigation.admin.css' );
		wp_enqueue_script( 'tab-nav-scripts-admin', CMB2_EXP_URL . 'assets/tab-navigation.admin.js', array('jquery'), false, true );
	}
	add_action( 'admin_enqueue_scripts', 'tabs_enqueue' );

	//function to check metaboxes specifically created with CMB2
	function find_cmb2_boxes( $arr ) {
		if ( is_a( $arr, 'CMB2_HOOKUP' ) ) return true;

		if ( is_array( $arr ) ) {
			foreach ( $arr as $element ) {
				if ( find_cmb2_boxes($element) ) return true;
			}
		}
	}

?>
