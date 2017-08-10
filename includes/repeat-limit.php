<?php
	/*
		Add 'repeat_limit' for repeatable meta boxes
	*/

	//TO DO: add variable to check if any repeatables, if false do not run admin_footer function

	add_action( 'cmb2_after_form', 'cmb2_repeat_limit', 10, 4 );
	function cmb2_repeat_limit( $cmb_id = array(), $object_id = null, $object_type = '', $cmb = array() ) {

		$repeatGroups = array();
		$repeatFields = array();

		$fields = $cmb->meta_box['fields'];

		foreach( $fields as $id => $field ) {

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
					if ( ! empty( $repeatGroups ) ) {

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

	add_action( 'admin_footer', 'cmb2_repeat_limit_admin_footer' );
	function cmb2_repeat_limit_admin_footer() {
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

 					if(type == 'group') {

						countRows = function() {
							return fieldTable.find( '> .cmb-row.cmb-repeatable-grouping' ).length;
						};

						disableAdder = function() {
							fieldTable.find('.cmb-add-group-row.button').prop( 'disabled', true );
						};

						enableAdder  = function() {
							fieldTable.find('.cmb-add-group-row.button').prop( 'disabled', false );
						};

					} else if(type == 'field') {

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

?>
