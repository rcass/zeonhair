<?php

/**
 * Retrieve the meta key used to store Builder data in
 *
 * @return string
 */
function themify_builder_get_meta_key() {
	return '_themify_builder_settings_json';
}

/**
 * Return Builder data for a post
 *
 * @param $post_id ID of the post to retrieve the data
 * @return array|bool
 */
function themify_builder_get_data( $post_id ) {
	if( metadata_exists( 'post', $post_id, themify_builder_get_meta_key() ) ) {
		$data = get_post_meta( $post_id, themify_builder_get_meta_key(), true );
		$data = stripslashes_deep( json_decode( $data, true ) );

		return $data;
	}

	return false;
}

/**
 * Check if a given post has Builder data attached to it
 *
 * @param $post_id ID of the post to check for Builder data
 * @return bool
 */
function themify_builder_has_builder_data( $post_id ) {
	$has_data = false;
	if( $data = themify_builder_get_data( $post_id ) ) {
		if( is_array( $data ) ) {
			$has_data = true;

			// validate saved empty data
			if( count( $data ) == 1 && themify_builder_is_row_empty( $data[0] ) ) {
				$has_data = false;
			}

		}
	}

	return $has_data;
}

/**
 * Validate an array to be a Builder row that contains data
 *
 * @return bool
 */
function themify_builder_is_row_empty( $row ) {
	if (
		( ! isset( $row['cols'] ) && ! isset( $row['styling'] ) )
		|| ( isset( $row['cols'] ) && empty( $row['cols'] ) && ! isset( $row['styling'] ) )
		|| ( isset( $row['cols'] ) && count( $row['cols'] ) == 1 && empty( $row['cols'][0]['modules'] ) && ( ! isset( $row['styling'] ) || empty( $row['styling'] ) ) ) // there's only one column and it's empty
	) {
		return false;
	}

	return true;
}