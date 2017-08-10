<?php

class CMB2_Repeat_Limit {

	static $instance = false;

	public function __construct() {
		$this->_add_actions();
	}

	/**
	 * Add metabox in User panel to opt out of plugin options
	 */
	public function user_tab_metabox() {

		// Only add the metabox if it wasn't created previously
		$cmb2_exp_user_box	= CMB2_Boxes::get('cmb2-exp-user-box');

		if ( $cmb2_exp_user_box !== false ) {

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

		}

		$cmb2_exp_user->add_field( array(
			'id'		=> 'cmb2-close-repeatable-switch',
			'name'		=> __( 'Collapse Repeatable', 'cmb2-exp' ),
			'desc'		=> __( 'If box is checked on, repeatable groups will NOT collapse automatically if there is more than one present.', 'cmb2-exp' ),
			'type'		=> 'checkbox',
			'on_front'	=> false
		) );

	}

	/**
	 * Enqueue General Assets
	 */
	public function enqueue_assets() {
		wp_enqueue_script( 'cmb2-scripts-admin', CMB2_EXP_URL . 'assets/cmb2-exp.admin.js', array( 'jquery' ), false );
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
			if ( isset($field['repeat_limit']) && $field['type'] == 'group' ) {

				// push to array to generate limits
				array_push(
					$repeatGroups,
					array(
						'id' => $field['id'],
						'limit' => $field['repeat_limit']
					)
				);

			// check that 'repeatable' and 'repeat_limit' are defined, as well as if it's NOT a group
			} elseif ( isset($field['repeatable']) && isset($field['repeat_limit']) && $field['type'] != 'group' ) {

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
					if ( ! empty( $repeatGroups ) ) {

						foreach ($repeatGroups as $k => $v) {
							$output .= "\n\t\t". sprintf( "repeatGroups.push(['%s', %s]);", $v['id'], $v['limit'] );
						}

						$output .= "\n\t\t". 'for(i = 0; repeatGroups.length > i; i++) {';
							$output .= "\n\t\t\t". 'var id = repeatGroups[i][0], limit = repeatGroups[i][1];';
							$output .= 'setTheLimit("group", id, limit)';
						$output .= "\n\t\t". '};';

					}

					// check if repeat array for fields exists
					if ( !empty( $repeatFields ) ) {

						foreach ($repeatFields as $k => $v) {
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

 					if (type == 'group') {

						countRows = function() {
							return fieldTable.find( '> .cmb-row.cmb-repeatable-grouping' ).length;
						};

						disableAdder = function() {
							fieldTable.find('.cmb-add-group-row.button').prop( 'disabled', true );
						};

						enableAdder  = function() {
							fieldTable.find('.cmb-add-group-row.button').prop( 'disabled', false );
						};

					} else if (type == 'field') {

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

		// admin actions
		add_action( 'admin_print_scripts-post-new.php', array( $this, 'enqueue_assets' ) );
		add_action( 'admin_print_scripts-post.php', array( $this, 'enqueue_assets' ) );

		// CMB Repeat Limit
		add_action( 'admin_footer', array( $this, 'repeat_limit_admin_footer' ) );
		add_action( 'cmb2_after_form', array( $this, 'repeat_limit' ), 10, 4 );

	}

}
