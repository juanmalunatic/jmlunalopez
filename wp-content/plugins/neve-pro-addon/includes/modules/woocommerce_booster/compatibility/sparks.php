<?php
/**
 * Sparks Compatibility
 *
 * @package Neve_Pro\Modules\Woocommerce_Booster\Compatibility
 */
namespace Neve_Pro\Modules\Woocommerce_Booster\Compatibility;

use Neve_Pro\Modules\Woocommerce_Booster\Compatibility\Sparks_Dependency_Check;
use Neve_Pro\Traits\Core;

/**
 * Class Sparks
 */
class Sparks {
	use Core;
	const MIN_WOOBOOSTER_LICENSE_REQ = 2;

	/**
	 * Initialization
	 *
	 * @return void
	 */
	public function init() {
		if ( ! defined( 'WC_VERSION' ) ) {
			return;
		}

		if ( ! $this->is_woobooster_available_for_license() ) {
			return;
		}

		( new Sparks_Install_Plugin() )->init();

		// TODO: replace woocommerce status with native module method \Neve_Pro\Modules\Woocommerce_Booster\Module::is_active() once automatic module configurations has been implemented.
		$is_woobooster_enabled = get_option( 'nv_pro_woocommerce_booster_status', true );

		if ( ! $is_woobooster_enabled ) {
			return;
		}

		add_action( 'admin_init', array( ( new Sparks_Dependency_Check() ), 'init' ) );

		if ( ! function_exists( 'sparks' ) ) {
			return;
		}

		$this->legacy_skin_compatibility();
	}

	/**
	 * Checks if module is available for current license.
	 *
	 * @return bool
	 */
	private function is_woobooster_available_for_license() {
		$availability = $this->get_license_type();

		if ( $availability >= self::MIN_WOOBOOSTER_LICENSE_REQ ) {
			return true;
		}

		return false;
	}

	/**
	 * Legacy Neve Skin compatibility
	 *
	 * @return void
	 */
	private function legacy_skin_compatibility() {
		if ( false !== neve_pro_is_new_skin() ) {
			return;
		}

		// Block style of Sparks Wish List if Neve skin is legacy one.
		add_filter( 'sparks_needs_enqueue_style', [ $this, 'block_wishlist_style' ], 10, 2 );
		add_filter( 'sparks_needs_module_dynamic_style', [ $this, 'block_wishlist_dynamic_style' ], 10, 2 );

		// Block style of Sparks Quick View if Neve skin is legacy one.
		add_filter( 'sparks_needs_enqueue_style', [ $this, 'block_quick_view_style' ], 10, 2 );
		add_filter( 'sparks_needs_module_dynamic_style', [ $this, 'block_quick_view_dynamic_style' ], 10, 2 );

		// Disable comparison table for legacy skin users.
		add_filter( 'sparks_module_check_dependencies', [ $this, 'disable_comparison_table_for_legacy_skin' ], 10, 2 );
		add_filter( 'sparks_module_dependency_errors', [ $this, 'add_error_msg_for_comparison_table' ], 10, 2 );
	}

	/**
	 * Show a message on comparison table setting to explain comparison table does not compatible with Neve legacy skin.
	 *
	 * @param  string[] $dependency_errors Dependency error messages.
	 * @param  string   $module_slug Module slug.
	 * @return string[]
	 */
	public function add_error_msg_for_comparison_table( array $dependency_errors, string $module_slug ): array {
		if ( 'comparison_table' !== $module_slug ) {
			return $dependency_errors;
		}

		$dependency_errors[] = __( 'Comparison Table Module does not compatible with Neve legacy skin.', 'neve' );

		return $dependency_errors;
	}

	/**
	 * Disable comparison table for legacy Neve skin users.
	 *
	 * @param  bool   $status Current status of dependency checking.
	 * @param  string $module_slug Module slug.
	 * @return bool
	 */
	public function disable_comparison_table_for_legacy_skin( bool $status, string $module_slug ): bool {
		if ( 'comparison_table' !== $module_slug ) {
			return $status;
		}

		return false;
	}

	/**
	 * Block dynamic style of Sparks Quick View
	 *
	 * @param  bool   $should_load Current loading status.
	 * @param  string $mdoule_slug Module slug of the dynamic styles.
	 * @return bool
	 */
	public function block_quick_view_dynamic_style( bool $should_load, string $mdoule_slug ): bool {
		if ( 'quick_view' === $mdoule_slug ) {
			return false;
		}

		return $should_load;
	}

	/**
	 * Block style of Sparks Quick View.
	 *
	 * @param  bool   $should_load Current loading status.
	 * @param  string $handle Name of the stylesheet.
	 * @return bool
	 */
	public function block_quick_view_style( bool $should_load, string $handle ): bool {
		if ( 'sparks-qv-style' === $handle ) {
			return false;
		}

		return $should_load;
	}

	/**
	 * Block dynamic style of Sparks Wish List
	 *
	 * @param  bool   $should_load Current loading status.
	 * @param  string $mdoule_slug Module slug of the dynamic styles.
	 * @return bool
	 */
	public function block_wishlist_dynamic_style( bool $should_load, string $mdoule_slug ): bool {
		if ( 'wish_list' === $mdoule_slug ) {
			return false;
		}

		return $should_load;
	}

	/**
	 * Block style of Sparks Wish List.
	 *
	 * @param  bool   $should_load Current loading status.
	 * @param  string $handle Name of the stylesheet.
	 * @return bool
	 */
	public function block_wishlist_style( bool $should_load, string $handle ): bool {
		// disable style of the Sparks.
		if ( 'sparks-wl-style' === $handle ) {
			return false;
		}

		return $should_load;
	}
}
