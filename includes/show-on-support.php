<?php
	/*
		Add support in 'show_on' option
	*/

	// add page-slug support
	add_filter( 'cmb2_show_on', 'metabox_show_on_slug', 10, 2 );
	function metabox_show_on_slug( $display, $meta_box ) {
		if ( ! isset( $meta_box['show_on']['key'], $meta_box['show_on']['value'] ) ) { return $display; }

		if ( 'slug' !== $meta_box['show_on']['key'] ) { return $display; }

		$post_id = 0;

		// If we're showing it based on ID, get the current ID
		if ( isset( $_GET['post'] ) ) {  $post_id = $_GET['post']; }
		elseif ( isset( $_POST['post_ID'] ) ) { $post_id = $_POST['post_ID']; }

		if ( ! $post_id ) { return $display; }

		$slug = get_post( $post_id )->post_name;

		// See if there's a match
		return in_array( $slug, (array) $meta_box['show_on']['value']);
	}

	// add front-page only view support
	add_filter( 'cmb2_show_on', 'metabox_include_front_page', 10, 2 );
	function metabox_include_front_page( $display, $meta_box ) {
		if ( ! isset( $meta_box['show_on']['key'] ) ) { return $display; }

		if ( 'front-page' !== $meta_box['show_on']['key'] ) { return $display; }

		$post_id = 0;

		// If we're showing it based on ID, get the current ID
		if ( isset( $_GET['post'] ) ) { $post_id = $_GET['post']; }
		elseif ( isset( $_POST['post_ID'] ) ) { $post_id = $_POST['post_ID']; }

		if ( ! $post_id ) { return $display; }

		// Get ID of page set as front page, 0 if there isn't one
		$front_page = get_option( 'page_on_front' );

		// there is a front page set and we're on it!
		return $post_id == $front_page;
	}

	// add exclude on page-slug support
	add_filter( 'cmb2_show_on', 'metabox_exclude_on_slug', 10, 2 );
	function metabox_exclude_on_slug( $display, $meta_box ) {
		if ( ! isset( $meta_box['show_on']['key'] ) ) { return $display; }

		if ( 'exclude_slug' !== $meta_box['show_on']['key'] ) { return $display; };

		// Get the current ID
		if ( isset( $_GET['post'] ) ) $post_id = $_GET['post'];
		elseif ( isset( $_POST['post_ID'] ) ) $post_id = $_POST['post_ID'];
		if ( !isset( $post_id ) ) return false;

		$slug = get_post( $post_id )->post_name;

		// If value isn't an array, turn it into one
		$meta_box['show_on']['value'] = !is_array( $meta_box['show_on']['value'] ) ? array( $meta_box['show_on']['value'] ) : $meta_box['show_on']['value'];

		// See if there's a match
		if ( in_array( $slug, (array) $meta_box['show_on']['value']) ) {
			return false;
		} else {
			return true;
		}
	}

	// for excluding CMB2s from post-new
	function cmb2_exclude_from_new( $display, $meta_box ) {
		if ( !isset($_GET['post']) ) {
			return;
		}

		return $display;
	}
	add_filter( 'cmb2_show_on', 'cmb2_exclude_from_new', 10, 2 );
