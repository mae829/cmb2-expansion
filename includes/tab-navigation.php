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
		global $post;
		global $wp_meta_boxes;

		$tabs             = '';
		$screen           = get_current_screen();
		$hidden_mbxs      = get_hidden_meta_boxes( $screen ); // hidden meta boxes in the "screen options" section
		$supports_editor  = post_type_supports( $post->post_type, 'editor' );

		//for ALL metaboxes that aren't part of the sidebar
		foreach ( $wp_meta_boxes[$post->post_type] as $mbx_location => $mbxs_priority ) {
			if ( $mbx_location != 'side' ) { // exclude metaboxes on sidebar (normal and advanced only)
				foreach ( $mbxs_priority as $priority => $boxes ) { // priorities: core, default, high, low
					foreach ( $boxes as $mbx_name => $box ) { // finally at each box

						// create tabs for all metaboxes, but add class HIDDEN for hidden metaboxes in "screen options"
						$display = in_array($mbx_name, $hidden_mbxs) ? ' hidden' : '';
						$tabs .= '<a class="nav-tab' . $display . '" href="#' . $box['id'] . '">' . $box['title'] . '</a>'; // ALL THAT FOR THIS!!!!

					}// end foreach boxes
				}// end foreach priorities
			}// end if not 'side' metaboxes
		}// end of ridiculous waterfall

		if ( $tabs != '' ) { ?>
			<div id="cmb2-nav">
				<h2><?php echo ucfirst($post->post_type); ?> Navigation:</h2>
				<div class="nav-tab-wrapper">
					<?php if ( $supports_editor ) : // only show tab if supports Post Editor ?>
					<a class="nav-tab" href="#postdivrich">Post Editor</a>
					<?php
						endif;
						echo $tabs;
					?>
				</div>
			</div>
		<?php }
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
