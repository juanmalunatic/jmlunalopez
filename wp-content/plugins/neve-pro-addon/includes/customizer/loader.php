<?php
/**
 * The customizer addons loader class.
 *
 * Author:          Andrei Baicus <andrei@themeisle.com>
 * Created on:      2018-12-03
 *
 * @package Neve Pro Addon
 */

namespace Neve_Pro\Customizer;

use Neve\Core\Factory;
use Neve_Pro\Admin\Conditional_Display;
use Neve_Pro\Traits\Core;

/**
 * Class Loader
 *
 * @since   0.0.1
 * @package Neve Pro Addon
 */
class Loader {
	use Core;

	const CUSTOMIZER_WC_SPARKS_NOTICE_HIDE_OPT_KEY = 'nv_pro_hide_sparks_customizer_notice';

	/**
	 * Customizer modules.
	 *
	 * @access private
	 * @since  0.0.1
	 * @var array
	 */
	private $modules = array();

	/**
	 * Loader constructor.
	 *
	 * @access public
	 * @since  0.0.1
	 */
	public function __construct() {
		add_action( 'customize_preview_init', array( $this, 'enqueue_customizer_preview' ) );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_customizer_controls' ) );
	}

	/**
	 * Initialize the customizer functionality
	 *
	 * @access public
	 * @since  0.0.1
	 */
	public function init() {
		global $wp_customize;

		if ( ! isset( $wp_customize ) ) {
			return;
		}

		$this->define_modules();
		$this->load_modules();
	}

	/**
	 * Define the modules that will be loaded.
	 *
	 * @access private
	 * @since  0.0.1
	 */
	private function define_modules() {
		$this->modules = apply_filters(
			'neve_pro_filter_customizer_modules',
			array(
				'Customizer\Options\Main',
			)
		);
	}

	/**
	 * Enqueue customizer controls script.
	 *
	 * @access public
	 * @since  0.0.1
	 */
	public function enqueue_customizer_controls() {
		// Legacy controls.
		wp_enqueue_script( 'neve-pro-controls', NEVE_PRO_INCLUDES_URL . 'customizer/controls/js/build/bundle.js', array( 'customize-controls' ), NEVE_PRO_VERSION, true );
		$this->rtl_enqueue_style( 'neve-pro-controls', NEVE_PRO_INCLUDES_URL . 'customizer/controls/css/customizer-controls.min.css', array(), NEVE_PRO_VERSION );
		$editor_dependencies = include_once plugin_dir_path( __FILE__ ) . 'controls/react/build/controls.asset.php';

		wp_register_script( 'neve-pro-react-controls', NEVE_PRO_INCLUDES_URL . 'customizer/controls/react/build/controls.js', $editor_dependencies['dependencies'], $editor_dependencies['version'], true );

		$localization = apply_filters(
			'neve_pro_react_controls_localization',
			[
				'conditionalRules' => $this->get_conditional_rules_array(),
				'headerControls'   => [ 'hfg_header_layout' ],
				'newBuilder'       => function_exists( 'neve_is_new_builder' ) && neve_is_new_builder(),
				'multiSelectRules' => Conditional_Display::MULTISELECT_RULES,
				'conditionTextMap' => Conditional_Display::create_custom_layouts_condition_text_map(),
				'sparksNotice'     => $this->show_sparks_notice_in_customizer() ? sprintf(
					/* translators: %s is link of Sparks dashboard */
					__( 'Please note that some of the options previously available in the WooCommerce Booster module are now part of the Sparks for WooCommerce settings page in %s', 'neve' ),
					/* translators: %s is link text */
					sprintf(
						'<strong><a href="' . get_dashboard_url( 0, 'options-general.php?page=sparks' ) . '" target="_blank">%s</a></strong>',
						__( 'Dashboard -> Settings', 'neve' )
					)
				) : '',
			]
		);

		wp_localize_script( 'neve-pro-react-controls', 'NeveProReactCustomize', $localization );
		wp_enqueue_script( 'neve-pro-react-controls' );

		$this->rtl_enqueue_style( 'neve-pro-react-controls', NEVE_PRO_INCLUDES_URL . 'customizer/controls/react/build/style-controls.css', [ 'wp-components' ], NEVE_PRO_VERSION );
	}

	/**
	 * Should be shown a customizer notice regarding with Sparks plugin in Customizer->WooCommerce panel?
	 *
	 * @return bool
	 */
	private function show_sparks_notice_in_customizer(): bool {
		return ( ! get_option( self::CUSTOMIZER_WC_SPARKS_NOTICE_HIDE_OPT_KEY, false ) ) && defined( 'SPARKS_WC_VERSION' );
	}

	/**
	 * Enqueue customizer preview script.
	 *
	 * @access public
	 * @since  0.0.1
	 */
	public function enqueue_customizer_preview() {
		$handle              = 'neve-pro-customize-preview';
		$editor_dependencies = include_once plugin_dir_path( __FILE__ ) . 'controls/react/build/customize-preview.asset.php';

		wp_register_script( $handle, NEVE_PRO_INCLUDES_URL . 'customizer/controls/react/build/customize-preview.js', $editor_dependencies['dependencies'], $editor_dependencies['version'], true );
		wp_enqueue_script( $handle );
	}

	/**
	 * Load the customizer modules.
	 *
	 * @access private
	 * @return void
	 * @since  0.0.1
	 */
	private function load_modules() {
		$factory = new Factory( $this->modules, '\\Neve_Pro\\' );
		$factory->load_modules();
	}

	/**
	 * Get the conditional rules array.
	 *
	 * @return array
	 */
	private function get_conditional_rules_array() {
		$conditional_display = new Conditional_Display();

		return [
			'root' => $conditional_display->get_root_ruleset(),
			'end'  => $conditional_display->get_end_ruleset(),
			'map'  => $conditional_display->get_ruleset_map(),
		];
	}
}
