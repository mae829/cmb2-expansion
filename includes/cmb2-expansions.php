<?php

class CMB2_Expansions {

	static $instance = false;

	public function __construct() {
		$this->_add_actions();
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

	// render numbers
	public function render_text_number( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
		echo $field_type_object->input( array( 'class' => 'cmb2-text-small', 'type' => 'number' ) );
	}

	// sanitize the field
	public function sanitize_text_number( $null, $new ) {
		$new = preg_replace( "/[^0-9]/", "", $new );

		return $new;
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

		// CMB2 Show On Support
		add_filter( 'cmb2_show_on', array( $this, 'metabox_show_on_front_page' ), 10, 2 );
		add_filter( 'cmb2_show_on', array( $this, 'metabox_show_on_slug' ), 10, 2 );
		add_filter( 'cmb2_show_on', array( $this, 'metabox_exclude_on_slug' ), 10, 2 );

		// text_number type field
		add_action( 'cmb2_render_text_number', array( $this, 'render_text_number' ), 10, 5 );
		add_filter( 'cmb2_sanitize_text_number', array( $this, 'sanitize_text_number' ), 10, 2 );

	}

}
