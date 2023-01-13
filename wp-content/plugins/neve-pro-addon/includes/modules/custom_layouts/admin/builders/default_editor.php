<?php
/**
 * Replace header, footer or hooks with the default editor.
 *
 * @package Neve_Pro\Modules\Custom_Layouts\Admin\Builders
 */

namespace Neve_Pro\Modules\Custom_Layouts\Admin\Builders;

use Neve_Pro\Modules\Custom_Layouts\Module;
use Neve_Pro\Traits\Core;

/**
 * Class Default_Editor
 *
 * @package Neve_Pro\Modules\Custom_Layouts\Admin\Builders
 */
class Default_Editor extends Abstract_Builders {
	use Core;


	/**
	 * Default_Editor constructor.
	 */
	public function __construct() {

	}

	/**
	 * Check if class should load or not.
	 *
	 * @return bool
	 */
	public function should_load() {
		return true;
	}

	/**
	 * Function that enqueues styles if needed.
	 */
	public function add_styles() {
		return false;
	}

	/**
	 * Builder id.
	 *
	 * @return string
	 */
	function get_builder_id() {
		return 'default';
	}

	/**
	 * Load markup for current hook.
	 *
	 * @param int $post_id Layout id.
	 *
	 * @return true
	 */
	function render( $post_id ) {
		global $post;
		$original_post = $post;
		$post_id       = Abstract_Builders::maybe_get_translated_layout( $post_id );
		setup_postdata( $post_id );

		$post    = get_post( $post_id );//phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$content = get_the_content( null, false, $post );
		$content = apply_filters( 'the_content', $content );
		echo apply_filters( 'neve_custom_layout_magic_tags', $content, $post_id ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		$post = $original_post;//phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		wp_reset_postdata();
		setup_postdata( $original_post );
		return true;
	}


}
