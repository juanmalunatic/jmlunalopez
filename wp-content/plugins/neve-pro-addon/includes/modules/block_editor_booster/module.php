<?php
/**
 * Block Editor Booster Module main file.
 *
 * @package Neve_Pro\Modules\Block_Editor_Booster
 */

namespace Neve_Pro\Modules\Block_Editor_Booster;

use Neve_Pro\Core\Abstract_Module;

use ThemeisleSDK\Product;

/**
 * Class Module
 *
 * @package Neve_Pro\Modules\Block_Editor_Booster
 */
class Module extends Abstract_Module {

	/**
	 * Holds the base module namespace
	 * Used to load submodules.
	 *
	 * @var string $module_namespace
	 */
	private $module_namespace = 'Neve_Pro\Modules\Block_Editor_Booster';

	/**
	 * Is Otter New
	 *
	 * @var boolean
	 */
	private $is_otter_new;

	/**
	 * Is Otter New
	 *
	 * @var boolean
	 */
	private $has_otter_pro;

	/**
	 * Conditional_Display constructor.
	 */
	public function __construct() {
		define( 'NEVE_PRO_HIDE_UPDATE_NOTICE', true );
		$this->is_otter_new  = defined( 'OTTER_BLOCKS_VERSION' ) && defined( 'OTTER_BLOCKS_PRO_SUPPORT' );
		$this->has_otter_pro = defined( 'OTTER_PRO_VERSION' );
		add_action( 'admin_post_install_otter_pro', array( $this, 'install_pro' ) );
		add_filter( 'neve_dashboard_notifications', array( $this, 'add_dashboard_notifications' ), 1 );
		parent::__construct();
	}

	/**
	 * Define module properties.
	 *
	 * @access  public
	 * @return void
	 *
	 * @version 1.0.0
	 */
	public function define_module_properties() {
		$this->slug            = 'block_editor_booster';
		$this->name            = __( 'Block Editor Booster', 'neve' );
		$this->description     = __( 'Do more with the Block Editor with Otter\'s additional blocks made specifically for Neve Pro.', 'neve' );
		$this->order           = 5;
		$this->min_req_license = 1;

		if ( ! $this->license_includes_otter() && ! $this->has_otter_pro ) {
			$this->min_req_license = 3;
		}

		$this->dependent_plugins = array(
			'otter-blocks' => array(
				'path' => 'otter-blocks/otter-blocks.php',
				'name' => 'Gutenberg Blocks and Template Library by Otter',
			),
			'otter-pro'    => array(
				'path'     => 'otter-pro/otter-pro.php',
				'name'     => 'Otter Pro',
				'external' => esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=install_otter_pro' ), 'install_otter_pro' ) ),
			),
		);

		$this->links         = array(
			array(
				'url'   => admin_url( 'options-general.php?page=otter' ),
				'label' => __( 'Settings', 'neve' ),
			),
		);
		$this->documentation = array(
			'url'   => 'https://bit.ly/nv-gb-bl',
			'label' => __( 'Learn more', 'neve' ),
		);
	}

	/**
	 * Check if module should be loaded.
	 *
	 * @return bool
	 */
	function should_load() {
		return ( $this->is_active() && defined( 'OTTER_BLOCKS_VERSION' ) );
	}

	/**
	 * Run Block Editor Booster Module
	 */
	function run_module() {
		add_filter( 'neve_has_block_editor_module', '__return_true' );
	}

	/**
	 * Wrapper for wp_remote_get on VIP environments.
	 *
	 * @param string $url Url to check.
	 * @param array  $args Option params.
	 *
	 * @return array|\WP_Error
	 */
	public function safe_get( $url, $args = array() ) {
		return function_exists( 'vip_safe_wp_remote_get' )
			? vip_safe_wp_remote_get( $url )
			: wp_remote_get( //phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.wp_remote_get_wp_remote_get, Already used.
				$url,
				$args
			);
	}

	/**
	 * Install Otter Pro.
	 */
	public function install_pro() {
		if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'install_otter_pro' ) || ! defined( 'OTTER_BLOCKS_VERSION' ) ) { //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			wp_nonce_ays( '' );
		}

		$response = $this->safe_get(
			sprintf(
				'%slicense/version/%s/%s/%s/%s',
				Product::API_URL,
				rawurlencode( 'Otter Pro' ),
				apply_filters( 'product_neve_license_key', 'free' ),
				OTTER_BLOCKS_VERSION,
				rawurlencode( home_url() )
			),
			array(
				'timeout'   => 15, //phpcs:ignore WordPressVIPMinimum.Performance.RemoteRequestTimeout.timeout_timeout, Inherited by wp_remote_get only, for vip environment we use defaults.
				'sslverify' => false,
			)
		);

		if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) ) {
			return false;
		}

		$update_data = json_decode( wp_remote_retrieve_body( $response ) );
		/* translators: %s: plugin update url*/
		$title     = __( 'Installing Otter Pro', 'neve' );
		$nonce     = 'upload-otter-pro';
		$overwrite = 'update-plugin';

		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		$upgrader = new \Plugin_Upgrader( new \Plugin_Installer_Skin( compact( 'title', 'nonce', 'overwrite' ) ) );
		echo '<title>' . esc_html( $title ) . '</title>';
		echo '<style>#error-page { word-break: break-all; }</style>';
		$upgrader->install( $update_data->download_link );
		wp_die();
	}

	/**
	 * Add Dashboard Notifications.
	 */
	public function add_dashboard_notifications( $notifications ) {
		$is_booster_active      = true === boolval( get_option( 'nv_pro_block_editor_booster_status', true ) ) && 'valid' === apply_filters( 'product_neve_license_status', false ) && defined( 'OTTER_BLOCKS_VERSION' );
		$is_otter_new           = defined( 'OTTER_BLOCKS_VERSION' ) && defined( 'OTTER_BLOCKS_PRO_SUPPORT' );
		$installed_plugins      = get_plugins();
		$is_otter_pro_installed = array_key_exists( 'otter-pro/otter-pro.php', $installed_plugins );
		$is_otter_pro_active    = defined( 'OTTER_PRO_VERSION' );
		$plugin_folder          = defined( 'OTTER_BLOCKS_PATH' ) ? basename( OTTER_BLOCKS_PATH ) : null;
		$plugin_path            = $plugin_folder ? $plugin_folder . '/otter-blocks.php' : null;

		if ( ! $this->license_includes_otter() && ! $this->has_otter_pro ) {
			return $notifications;
		}

		if ( $is_booster_active && ! $is_otter_new ) {
			$notifications['otter-old'] = [
				'text'   => __( 'You need to update Otter and install Otter Pro to continue using Block Editor Booster', 'neve' ),
				'update' => [
					'type' => 'otter',
					'slug' => 'otter-old',
					'path' => $plugin_path,
				],
				'cta'    => __( 'Update & Install', 'neve' ),
				'type'   => 'warning',
			];
		}

		if ( $is_booster_active && $is_otter_new && ! $is_otter_pro_installed && ! $is_otter_pro_active ) {
			$notifications['otter-new'] = [
				'text'   => __( 'You need to install Otter Pro to continue using Block Editor Booster', 'neve' ),
				'update' => [
					'type' => 'otter',
					'slug' => 'otter-new',
					'path' => $plugin_path,
				],
				'cta'    => __( 'Install', 'neve' ),
				'type'   => 'warning',
			];
		}

		if ( $is_booster_active && $is_otter_new && $is_otter_pro_installed && ! $is_otter_pro_active ) {
			$notifications['otter-new'] = [
				'text'   => __( 'You need to activate Otter Pro to continue using Block Editor Booster', 'neve' ),
				'update' => [
					'type' => 'otter',
					'slug' => 'otter-new',
					'path' => $plugin_path,
				],
				'cta'    => __( 'Activate', 'neve' ),
				'type'   => 'warning',
			];
		}

		return $notifications;
	}

	/**
	 * Initialize the module.
	 */
	public function init() {
		if ( $this->should_load() && ! $this->is_otter_new ) {
			add_action( 'admin_notices', array( $this, 'old_otter_notice' ) );
		}

		if ( $this->should_load() && $this->is_otter_new && ! $this->has_otter_pro ) {
			add_action( 'admin_notices', array( $this, 'new_otter_notice' ) );
		}
	}

	/**
	 * Notice displayed if using old version of Otter.
	 */
	function old_otter_notice() {
		$plugin_name = __( 'Block Editor Booster', 'neve' );
		$message     = __( 'You need to update Otter and install Otter Pro to continue using Block Editor Booster.', 'neve' );

		printf(
			'<div class="error"><p><b>%1$s</b> %2$s <a href="%3$s">%4$s</a></p></div>',
			esc_html( $plugin_name ),
			esc_html( $message ),
			esc_url( admin_url( 'themes.php?page=neve-welcome' ) ),
			esc_html__( 'Update & Install', 'neve' )
		);
	}

	/**
	 * Notice displayed if using new version of Otter without Pro.
	 */
	function new_otter_notice() {
		$plugin_name = __( 'Block Editor Booster', 'neve' );
		$message     = __( 'You need to install & activate Otter Pro to continue using Block Editor Booster.', 'neve' );

		printf(
			'<div class="error"><p><b>%1$s</b> %2$s <a href="%3$s">%4$s</a></p></div>',
			esc_html( $plugin_name ),
			esc_html( $message ),
			esc_url( admin_url( 'themes.php?page=neve-welcome' ) ),
			esc_html__( 'Install & Activate', 'neve' )
		);
	}
}

