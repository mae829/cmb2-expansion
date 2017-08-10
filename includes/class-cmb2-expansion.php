<?php

class CMB2_Expansion {

	static $instance = false;

	public function __construct() {
		$this->_add_actions();
	}

	/**
	 * Enqueue Tabs Assets
	 */
	public function enqueue_tabs_assets() {
		global $post;

		$post_type_show	= isset($post) && $post->post_type == 'attachment' ? false : true;

		// only enqueue these if CMB2 is present
		if ( wp_style_is( 'cmb2-styles', 'enqueued' ) && wp_script_is( 'cmb2-scripts', 'enqueued' ) && $post_type_show ) {
			wp_enqueue_style( 'cmb2-exp-nav-styles-admin', CMB2_EXP_URL . 'assets/cmb2-exp-tab-nav.admin.min.css', false, CMB2_EXP_VERSION, false );
			wp_enqueue_script( 'cmb2-exp-nav-scripts-admin', CMB2_EXP_URL . 'assets/cmb2-exp-tab-nav.admin.min.js', array('jquery'), CMB2_EXP_VERSION, true );
		}

	}

	/**
	 * Enqueue General Assets
	 */
	public function enqueue_assets() {
		wp_enqueue_script( 'cmb2-scripts-admin', CMB2_EXP_URL . 'assets/cmb2-exp.admin.js', array( 'jquery' ), false );
	}

	/**
	 * Metabox Include Front Page
	 *
	 * Called by the "cmb2_show_on" filter.
	 *
	 * @param bool $display Either TRUE or FALSE, default is TRUE
	 * @param array $meta_box Array of metabox parameters
	 * @param object The CMB object for the current metabox
	 */
	public function metabox_show_on_front_page( $display = true, $meta_box = array(), $cmb = null ) {

		if ( ! isset( $meta_box['show_on']['key'] ) ) {
			return $display;
		}

		if ( 'front-page' !== $meta_box['show_on']['key'] ) {
			return $display;
		}

		$post_id = 0;

		// If we're showing it based on ID, get the current ID
		if ( isset( $_GET['post'] ) ) {

			$post_id = $_GET['post'];

		} elseif ( isset( $_POST['post_ID'] ) ) {

			$post_id = $_POST['post_ID'];

		}

		if ( ! $post_id ) {
			return $display;
		}

		// Get ID of page set as front page, 0 if there isn't one
		$front_page = get_option( 'page_on_front' );

		// there is a front page set and we're on it!
		return ( $post_id == $front_page );

	}

	/**
	 * Metabox Show on Slug
	 *
	 * Called by the "cmb2_show_on" filter.
	 *
	 * @param bool $display Either TRUE or FALSE, default is TRUE
	 * @param array $meta_box Array of metabox parameters
	 * @param object The CMB object for the current metabox
	 */
	public function metabox_show_on_slug( $display = true, $meta_box = array(), $cmb = null ) {

		if ( ! isset( $meta_box['show_on']['key'], $meta_box['show_on']['value'] ) ) {
			return $display;
		}

		if ( 'slug' !== $meta_box['show_on']['key'] ) {
			return $display;
		}

		$post_id = 0;

		// If we're showing it based on ID, get the current ID
		if ( isset( $_GET['post'] ) ) {

			$post_id = $_GET['post'];

		} elseif ( isset( $_POST['post_ID'] ) ) {

			$post_id = $_POST['post_ID'];

		}

		if ( ! $post_id ) {
			return $display;
		}

		$slug = get_post( $post_id )->post_name;

		// See if there's a match
		return in_array( $slug, (array) $meta_box['show_on']['value'] );

	}

	/**
	 * Metabox Exclude on Slug
	 *
	 * Called by the "cmb2_show_on" filter.
	 *
	 * @param bool $display Either TRUE or FALSE, default is TRUE
	 * @param array $meta_box Array of metabox parameters
	 * @param object The CMB object for the current metabox
	 */
	public function metabox_exclude_on_slug( $display = true, $meta_box = array(), $cmb = null ) {

		if ( ! isset( $meta_box['show_on']['key'] ) ) {
			return $display;
		}

		if ( 'exclude_slug' !== $meta_box['show_on']['key'] ) {
			return $display;
		};

		// Get the current ID
		if ( isset( $_GET['post'] ) ) {

			$post_id = $_GET['post'];

		} elseif ( isset( $_POST['post_ID'] ) ) {

			$post_id = $_POST['post_ID'];

		}

		if ( !isset( $post_id ) ) {
			return false;
		}

		$slug = get_post( $post_id )->post_name;

		// If value isn't an array, turn it into one
		$meta_box['show_on']['value'] = !is_array( $meta_box['show_on']['value'] ) ? array( $meta_box['show_on']['value'] ) : $meta_box['show_on']['value'];

		// See if there's a match
		return !in_array( $slug, (array) $meta_box['show_on']['value'] );

	}

	/**
	 * Repeat Limit
	 *
	 * Called by the "cmb2_after_form" hook.
	 *
	 * @param array  $cmb_id      The current box ID
	 * @param int    $object_id   The ID of the current object
	 * @param string $object_type The type of object you are working with.
	 *                             Usually `post` (this applies to all post-types).
	 *                             Could also be `comment`, `user` or `options-page`.
	 * @param array  $cmb         This CMB2 object
	 */
	public function repeat_limit( $cmb_id = array(), $object_id = null, $object_type = '', $cmb = array() ) {

		$repeatGroups = array();
		$repeatFields = array();

		$fields = $cmb->meta_box['fields'];

		foreach ( $fields as $id => $field ) {

			// check that 'repeat_limit' is defined, as well as if it's a group
			if ( isset( $field['repeat_limit'] ) && $field['type'] == 'group' ) {

				// push to array to generate limits
				array_push(
					$repeatGroups,
					array(
						'id' => $field['id'],
						'limit' => $field['repeat_limit']
					)
				);

			// check that 'repeatable' and 'repeat_limit' are defined, as well as if it's NOT a group
			} elseif ( isset( $field['repeatable'] ) && isset( $field['repeat_limit'] ) && $field['type'] != 'group' ) {

				array_push(
					$repeatFields,
					array(
						'id' => $field['id'],
						'limit' => $field['repeat_limit']
					)
				);

			}
		}

		if ( !empty( $repeatGroups ) || !empty( $repeatFields ) ) {

			$output = '<script type="text/javascript">';

				$output .= "\n\t". 'jQuery(document).ready(function($) {';

					$output .= "\n\t\t". 'var i;';

					// check if repeat array for groups exists
					if ( !empty( $repeatGroups ) ) {

						foreach ( $repeatGroups as $k => $v ) {
							$output .= "\n\t\t". sprintf( "repeatGroups.push(['%s', %s]);", $v['id'], $v['limit'] );
						}

						$output .= "\n\t\t". 'for(i = 0; repeatGroups.length > i; i++) {';
							$output .= "\n\t\t\t". 'var id = repeatGroups[i][0], limit = repeatGroups[i][1];';
							$output .= 'setTheLimit("group", id, limit)';
						$output .= "\n\t\t". '};';

					}

					// check if repeat array for fields exists
					if ( !empty( $repeatFields ) ) {

						foreach ( $repeatFields as $k => $v ) {
							$output .= "\n\t\t". sprintf( "repeatFields.push(['%s', %s]);", $v['id'], $v['limit'] );
						}

						$output .= "\n\t\t". 'for(i = 0; repeatFields.length > i; i++) {';
							$output .= "\n\t\t\t". 'var id = repeatFields[i][0], limit = repeatFields[i][1];';
							$output .= "\n\t\t\t". 'setTheLimit("field", id, limit)';
						$output .= "\n\t\t". '};';
					}

				$output .= "\n\t". '})';

			$output .= "\n". '</script>';

			echo $output;
		}

	}

	/**
	 * Repeat Limit Admin Footer
	 */
	public function repeat_limit_admin_footer() {
		ob_start(); ?>

			<script type='text/javascript'>
				// add limit to repeatable groups
				var repeatGroups = [],
					repeatFields = [];

				function setTheLimit(type, id, limit) {
					var fieldTable = jQuery( document.getElementById( id + '_repeat' ) ),
						countRows     = '',
						disableAdder  = '',
						enableAdder   = '';

 					if ( type == 'group' ) {

						countRows = function() {
							return fieldTable.find( '> .cmb-row.cmb-repeatable-grouping' ).length;
						};

						disableAdder = function() {
							fieldTable.find('.cmb-add-group-row.button').prop( 'disabled', true );
						};

						enableAdder  = function() {
							fieldTable.find('.cmb-add-group-row.button').prop( 'disabled', false );
						};

					} else if ( type == 'field' ) {

						countRows    = function() {
							return fieldTable.find( '.cmb-row.cmb-repeat-row' ).length;
						};

						disableAdder = function() {
							fieldTable.parents( '.cmb-row.cmb-repeat' ).find('.cmb-add-row-button.button').prop( 'disabled', true );
						};

						enableAdder  = function() {
							fieldTable.parents( '.cmb-row.cmb-repeat' ).find('.cmb-add-row-button.button').prop( 'disabled', false );
						};

					}

					if ( countRows() >= limit ) {
						disableAdder();
					}

					fieldTable
						.on( 'cmb2_add_row', function() {
							if ( countRows() >= limit ) {
								disableAdder();
							}
						})
						.on( 'cmb2_remove_row', function() {
							if ( countRows() < limit ) {
								enableAdder();
							}
						});
				}; // end setTheLimit()
			</script>
		<?php
		$output	= ob_get_clean();
		echo $output;

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

		// admin actions
		add_action( 'admin_print_scripts-post-new.php', array( $this, 'enqueue_assets' ) );
		add_action( 'admin_print_scripts-post.php', array( $this, 'enqueue_assets' ) );
		add_action( 'admin_footer', array( $this, 'repeat_limit_admin_footer' ) );

		// CMB Tab Navigation
		add_action( 'admin_footer', array( $this, 'enqueue_tabs_assets' ), 10 );
		add_action( 'edit_form_after_title', array( $this, 'meta_tabs' ) );

		// CMB Repeat Limit
		add_action( 'cmb2_after_form', array( $this, 'repeat_limit' ), 10, 4 );

		// CMB2 Show On Support
		add_filter( 'cmb2_show_on', array( $this, 'metabox_show_on_front_page' ), 10, 3 );
		add_filter( 'cmb2_show_on', array( $this, 'metabox_show_on_slug' ), 10, 3 );
		add_filter( 'cmb2_show_on', array( $this, 'metabox_exclude_on_slug' ), 10, 3 );

	}

}
