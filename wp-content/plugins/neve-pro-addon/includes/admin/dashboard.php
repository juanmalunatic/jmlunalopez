<?php
/**
 * Author:          Andrei Baicus <andrei@themeisle.com>
 * Created on:      2019-01-28
 *
 * @package Neve Pro Addon
 */

namespace Neve_Pro\Admin;

use Neve_Pro\Core\Abstract_Module;
use Neve_Pro\Core\Loader;
use Neve_Pro\Traits\Core;

/**
 * Class Dashboard
 *
 * @package Neve Pro Addon
 */
class Dashboard {
	use Core;

	/**
	 * Neve Pro plugin name
	 *
	 * @var string
	 */
	private $plugin_name;

	/**
	 * The app script handle.
	 *
	 * @var string
	 */
	private $script_handle;

	/**
	 * The app endpoint.
	 *
	 * @var string
	 */
	private $rest_endpoint;

	/**
	 * Rest Routes Handler
	 *
	 * @var Rest_Server
	 */
	private $rest_server;


	/**
	 * Dashboard constructor.
	 */
	public function __construct() {
		$this->plugin_name   = apply_filters( 'ti_wl_plugin_name', NEVE_PRO_NAME );
		$this->script_handle = NEVE_PRO_NAMESPACE . '-dashboard-app';
		$this->rest_endpoint = NEVE_PRO_REST_NAMESPACE;
		$this->rest_server   = new Rest_Server( $this->rest_endpoint );
	}

	/**
	 * Initialize the module.
	 */
	public function init() {
		add_filter( 'neve_dashboard_page_data', [ $this, 'add_dashboard_data' ] );
		add_filter( 'ti_about_config_filter', [ $this, 'add_neve_pro_addons_tab_fallback' ], 20 );
		add_filter( 'ti_tpc_editor_data', [ $this, 'add_tpc_data' ], 20 );
	}

	/**
	 * Add about page tab list item.
	 *
	 * @param array $config about page config.
	 *
	 * @return array
	 */
	public function add_neve_pro_addons_tab_fallback( $config ) {
		$config['custom_tabs']['neve_pro_addons'] = [
			'title'           => $this->plugin_name,
			'render_callback' => [ $this, 'render_fallback_tab_content' ],
		];

		return $config;
	}

	/**
	 * Renders fallback content for the old version of the theme Pro Tab Content.
	 *
	 * @see add_neve_pro_addons_tab_fallback
	 */
	public function render_fallback_tab_content() {
		$theme      = wp_get_theme();
		$theme_name = apply_filters( 'ti_wl_theme_name', $theme->__get( 'Name' ) );
		echo '<h3>';
		/* translators: s - theme name (Neve) */
		echo esc_html( sprintf( __( 'Please update %s to the latest version and then refresh this page to have access to the options.', 'neve' ), ( $theme_name ) ) );
		echo '</h3>';
	}

	/**
	 * Adds the necessary pro dashboard data.
	 *
	 * @param array $data The dashboard localization data from the theme.
	 * @return array
	 */
	public function add_dashboard_data( $data ) {
		$index = apply_filters( 'product_neve_license_plan', -1 );

		return array_merge(
			$data,
			[
				'pro'          => true,
				'proApi'       => rest_url( $this->rest_endpoint ),
				'license'      => [
					'key'        => apply_filters( 'product_neve_license_key', 'free' ),
					'valid'      => apply_filters( 'product_neve_license_status', false ),
					'expiration' => $this->get_license_expiration_date(),
					'tier'       => $index > -1 ? $this->tier_map[ $index ] : -1,
				],
				'supportURL'   => esc_url( 'https://themeisle.com/contact/' ),
				'modules'      => $this->sort_modules( $this->get_modules() ),
				'upgradeLinks' => $this->get_upgrade_links(),
			]
		);
	}

	/**
	 * Adds the necessary pro TPC data.
	 *
	 * @param array $data The TPC editor localization data.
	 * @return array
	 */
	public function add_tpc_data( $data ) {
		$index = apply_filters( 'product_neve_license_plan', -1 );

		$data['tier'] = $index > -1 ? $this->tier_map[ $index ] : -1;

		return $data;
	}

	/**
	 * Get upgrade links.
	 *
	 * @return array
	 */
	private function get_upgrade_links() {
		return array(
			'1' => tsdk_utmify( 'https://themeisle.com/themes/neve/upgrade/', 'upgrade_link', 'neveprosettings' ),
			'2' => tsdk_utmify( 'https://themeisle.com/themes/neve/upgrade/', 'upgrade_link', 'neveprosettings' ),
			'3' => tsdk_utmify( 'https://themeisle.com/themes/neve/upgrade/', 'upgrade_link', 'neveprosettings' ),
		);
	}

	/**
	 * Utility method to sort modules by order key.
	 *
	 * @param array $modules The modules list.
	 *
	 * @return mixed
	 * @since   1.0.0
	 * @access  private
	 */
	private function sort_modules( $modules ) {
		uasort(
			$modules,
			function ( $item1, $item2 ) {
				if ( ! isset( $item1['order'] ) ) {
					return -1;
				}
				if ( ! isset( $item2['order'] ) ) {
					return -1;
				}
				if ( $item1['order'] === $item2['order'] ) {
					return 0;
				}

				return $item1['order'] < $item2['order'] ? -1 : 1;
			}
		);

		return $modules;
	}

	/**
	 * Get modules.
	 *
	 * For the unload option use classes from Neve_Pro\Core\Loader
	 *
	 * @return array
	 */
	private function get_modules() {

		$pluggable_modules = Loader::instance()->get_modules();
		$modules           = array();
		if ( ! empty( $pluggable_modules ) ) {
			/**
			 * Iterates over instances of Abstract_Module
			 *
			 * @var Abstract_Module $module A module instance.
			 */
			foreach ( $pluggable_modules as $module ) {
				$modules = array_merge( $modules, $module->get_module_info() );
			}
		}

		/**
		 * White label module
		 */
		$white_label_settings = get_option( 'ti_white_label_inputs' );
		$white_label_settings = json_decode( $white_label_settings, true );
		if ( isset( $white_label_settings['white_label'] ) ) {
			if ( $white_label_settings['white_label'] === true && isset( $modules['white_label'] ) ) {
				unset( $modules['white_label'] );
			}
		}

		return apply_filters( 'neve_pro_filter_dashboard_modules', $modules );
	}
}
