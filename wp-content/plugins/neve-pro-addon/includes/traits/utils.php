<?php
/**
 * Utility functions shared between modules.
 *
 * @package Neve_Pro
 */
namespace Neve_Pro\Traits;

use Neve\Customizer\Defaults\Layout;

/**
 * Trait Utils
 *
 * @package Neve_Pro\Traits
 */
trait Utils {
	use Layout;

	/**
	 * Get default meta value
	 */
	public function get_default_meta_value( $field, $default ) {
		if ( ! function_exists( 'neve_get_default_meta_value' ) ) {
			return Layout::get_meta_default_data( $field, $default );
		}

		return neve_get_default_meta_value( $field, $default );
	}
}
