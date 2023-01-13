<?php
/**
 * Sanitize functions.
 *
 * @package Neve_Pro\Modules\Blog_Pro\Customizer
 */
namespace Neve_Pro\Modules\Blog_Pro\Customizer;

use Neve_Pro\Modules\Blog_Pro\Customizer\Defaults\Single_Post;

/**
 * Trait Sanitize_Functions
 *
 * @package Neve_Pro\Modules\Blog_Pro\Customizer
 */
trait Sanitize_Functions {
	use Single_Post;

	/**
	 * Sanitize sharing order.
	 *
	 * @param string $value Value from the control.
	 *
	 * @return string
	 */
	public function sanitize_sharing_icons_repeater( $value ) {
		$default_value = apply_filters( 'neve_sharing_icons_default_value', $this->social_icons_default() );
		$fields        = array(
			'social_network',
			'visibility',
		);

		$valid = $this->sanitize_repeater_json( $value, $fields );

		if ( $valid === false ) {
			return wp_json_encode( $default_value );
		}

		return $value;
	}

	/**
	 * Sanitize the social sharing tag control.
	 *
	 * @param string $value Control value.
	 *
	 * @return string
	 */
	public function sanitize_sharing_icons_tag( $value ) {
		$allowed_tags = [ 'p', 'span', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ];
		if ( ! in_array( $value, $allowed_tags, true ) ) {
			return 'span';
		}
		return $value;
	}

	/**
	 * Sanitize the repeater control.
	 *
	 * @param string $value json value.
	 * @param array  $must_have_fields array of must have fields for repeater.
	 *
	 * @return bool
	 */
	public function sanitize_repeater_json( $value, $must_have_fields = array( 'visibility' ) ) {
		$decoded = json_decode( $value, true );

		if ( ! is_array( $decoded ) ) {
			return false;
		}
		foreach ( $decoded as $item ) {
			if ( ! is_array( $item ) ) {
				return false;
			}

			foreach ( $must_have_fields as $field_key ) {
				if ( ! array_key_exists( $field_key, $item ) ) {
					return false;
				}
			}
		}

		return true;
	}


}
