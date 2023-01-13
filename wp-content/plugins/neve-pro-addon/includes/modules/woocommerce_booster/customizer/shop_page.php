<?php
/**
 * Shop page WooCommerce Booster Module
 *
 * @package WooCommerce Booster
 */

namespace Neve_Pro\Modules\Woocommerce_Booster\Customizer;

use Neve\Customizer\Base_Customizer;
use Neve\Customizer\Types\Control;

/**
 * Class Shop_Page
 *
 * @package Neve_Pro\Modules\Woocommerce_Booster\Customizer
 */
class Shop_Page extends Base_Customizer {

	/**
	 * Init function sooner than WooCommerce.
	 */
	public function init() {
		add_action( 'customize_register', array( $this, 'register_controls_callback' ), 9 );
		add_filter( 'neve_last_menu_item_components', array( $this, 'add_wish_list_menu_option' ) );
		add_filter( 'neve_sidebar_layout_choices', array( $this, 'add_off_canvas_option' ), 10, 2 );
		add_action( 'customize_controls_print_styles', array( $this, 'elements_ordering_inline_alignment_style' ) );
		add_action( 'customize_controls_print_footer_scripts', array( $this, 'elements_ordering_inline_alignment_script' ) );
		add_action( 'customize_controls_print_footer_scripts', array( $this, 'handle_customizer_product_card_layout_switch' ) );
	}

	/**
	 * Hide price in Card content elements order in customizer.
	 */
	public function elements_ordering_inline_alignment_style() {
		$alignment = get_theme_mod( 'neve_product_content_alignment', 'left' );
		if ( $alignment !== 'inline' ) {
			return false;
		}
		echo '<style>';
		echo '#customize-control-neve_layout_product_elements_order .order-component[data-id="price"] { display: none; }';
		echo '</style>';

		return true;
	}

	/**
	 * Handle the customizer display when alignment settings are changing.
	 */
	public function elements_ordering_inline_alignment_script() {
		?>
		<script type="text/javascript">
				wp.customize.bind('ready', function(){
					wp.customize('neve_product_content_alignment', function (value) {
						value.bind(function (newval) {
							if (newval !== 'inline') {
								wp.customize.control('neve_layout_product_elements_order').params.components.title = '<?php echo esc_html__( 'Title', 'neve' ); ?>'
								wp.customize.control('neve_layout_product_elements_order').params.components.price = '<?php echo esc_html__( 'Price', 'neve' ); ?>'
							}
							if (newval === 'inline') {
								wp.customize.control('neve_layout_product_elements_order').params.components.title = '<?php echo esc_html__( 'Title + Price', 'neve' ); ?>'
								delete (wp.customize.control('neve_layout_product_elements_order').params.components.price)
							}
							wp.customize.control('neve_layout_product_elements_order').renderContent();
						})
					})
				})
		</script>
		<?php
	}

	/**
	 * Handle the Product card layout in customizer preview display when the option is being switched between grid/list.
	 */
	public function handle_customizer_product_card_layout_switch() {
		?>
		<script type="text/javascript">
				wp.customize.bind('ready', function(){
					wp.customize('neve_product_card_layout', function (value) {

						value.bind(function (newval) {

							const iframeElement = document.querySelector( '#customize-preview iframe' );

							if ( ! iframeElement ) {
								return;
							}

							const shopItemsContainer = iframeElement.contentWindow.document.querySelector(
								'.nv-shop'
							);
							const listLayoutToggle = iframeElement.contentWindow.document.querySelector(
								'.nv-toggle-list-view'
							);
							const gridLayoutToggle = iframeElement.contentWindow.document.querySelector(
								'.nv-toggle-grid-view'
							);

							if( newval === 'grid' ){
								shopItemsContainer.classList.remove('nv-list');
								if( listLayoutToggle && gridLayoutToggle ){
									gridLayoutToggle.classList.add('current');
									listLayoutToggle.classList.remove('current');
								}
							}else{
								shopItemsContainer.classList.add('nv-list');
								if( listLayoutToggle && gridLayoutToggle ){
									listLayoutToggle.classList.add('current');
									gridLayoutToggle.classList.remove('current');
								}
							}

						})
					})
				})
		</script>
		<?php
	}

	/**
	 * Add wish list item in last menu items options.
	 *
	 * @param array $items Last menu items options.
	 *
	 * @return mixed
	 */
	public function add_wish_list_menu_option( $items ) {
		$items['wish_list'] = __( 'Wish List', 'neve' );

		return $items;
	}

	/**
	 * Add customizer controls.
	 */
	public function add_controls() {
		$this->group_controls();
		$this->add_page_layout_controls();
		$this->add_product_card_layout_controls();
		$this->add_category_card_layout_controls();
		$this->add_card_image_controls();
		$this->add_card_content_controls();
		$this->add_sale_tag_controls();
	}

	/**
	 * Add control groups to better organize the customizer.
	 */
	private function group_controls() {
		$this->add_control(
			new Control(
				'neve_woo_shop_settings_heading',
				array(
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'            => esc_html__( 'General', 'neve' ),
					'section'          => 'woocommerce_product_catalog',
					'priority'         => 0,
					'class'            => 'woo-shop-accordion',
					'accordion'        => true,
					'controls_to_wrap' => 3,
					'expanded'         => true,
				),
				'Neve\Customizer\Controls\Heading'
			)
		);

		$this->add_control(
			new Control(
				'neve_shop_page_layout_ui_heading',
				array(
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'            => esc_html__( 'Page Layout', 'neve' ),
					'section'          => 'woocommerce_product_catalog',
					'priority'         => 100,
					'class'            => 'page-layout-accordion',
					'accordion'        => true,
					'controls_to_wrap' => 5,
					'expanded'         => true,
				),
				'Neve\Customizer\Controls\Heading'
			)
		);

		$this->add_control(
			new Control(
				'neve_product_card_layout_ui_heading',
				array(
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'            => esc_html__( 'Product Card', 'neve' ),
					'section'          => 'woocommerce_product_catalog',
					'priority'         => 200,
					'class'            => 'card-layout-accordion',
					'accordion'        => true,
					'controls_to_wrap' => 5,
					'expanded'         => false,
				),
				'Neve\Customizer\Controls\Heading'
			)
		);

		$this->add_control(
			new Control(
				'neve_category_card_layout_ui_heading',
				array(
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'            => esc_html__( 'Category Card', 'neve' ),
					'section'          => 'woocommerce_product_catalog',
					'priority'         => 300,
					'class'            => 'category-layout-accordion',
					'accordion'        => true,
					'controls_to_wrap' => 1,
					'expanded'         => false,
				),
				'Neve\Customizer\Controls\Heading'
			)
		);

		$this->add_control(
			new Control(
				'neve_card_image_ui_heading',
				array(
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'            => esc_html__( 'Card Image', 'neve' ),
					'section'          => 'woocommerce_product_catalog',
					'priority'         => 400,
					'class'            => 'card-image-accordion',
					'accordion'        => true,
					'controls_to_wrap' => 3,
					'expanded'         => false,
				),
				'Neve\Customizer\Controls\Heading'
			)
		);

		$this->add_control(
			new Control(
				'neve_card_content_ui_heading',
				array(
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'            => esc_html__( 'Card Content', 'neve' ),
					'section'          => 'woocommerce_product_catalog',
					'priority'         => 500,
					'class'            => 'card-content-accordion',
					'accordion'        => true,
					'controls_to_wrap' => 3,
					'expanded'         => false,
				),
				'Neve\Customizer\Controls\Heading'
			)
		);

		$this->add_control(
			new Control(
				'neve_sale_tag_ui_heading',
				array(
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'            => esc_html__( 'Sale Tag', 'neve' ),
					'section'          => 'woocommerce_product_catalog',
					'priority'         => 600,
					'class'            => 'sale-tag-accordion',
					'accordion'        => true,
					'controls_to_wrap' => 8,
					'expanded'         => false,
				),
				'Neve\Customizer\Controls\Heading'
			)
		);
	}

	/**
	 * Controls that refer to page layout.
	 */
	private function add_page_layout_controls() {
		$new_skin = neve_pro_is_new_skin();
		$this->add_control(
			new Control(
				'neve_products_per_row',
				array(
					'sanitize_callback' => 'neve_sanitize_range_value',
					'transport'         => $new_skin ? 'postMessage' : 'refresh',
					'default'           => wp_json_encode(
						array(
							'desktop' => $new_skin ? 3 : 4,
							'tablet'  => 2,
							'mobile'  => 2,
						)
					),
				),
				array(
					'label'                 => esc_html__( 'Products per row', 'neve' ),
					'section'               => 'woocommerce_product_catalog',
					'input_attr'            => array(
						'mobile'  => array(
							'min'     => 1,
							'max'     => 6,
							'default' => 1,
						),
						'tablet'  => array(
							'min'     => 1,
							'max'     => 6,
							'default' => 2,
						),
						'desktop' => array(
							'min'     => 1,
							'max'     => 6,
							'default' => $new_skin ? 3 : 4,
						),
					),
					'input_attrs'           => [
						'step'       => 1,
						'min'        => 1,
						'max'        => 6,
						'defaultVal' => [
							'mobile'  => 1,
							'tablet'  => 2,
							'desktop' => $new_skin ? 3 : 4,
						],
					],
					'priority'              => 110,
					'responsive'            => true,
					'active_callback'       => array( $this, 'products_per_row_active_callback' ),
					'live_refresh_selector' => $new_skin,
					'live_refresh_css_prop' => [
						'cssVar' => [
							'vars'       => '--shopcoltemplate',
							'selector'   => 'body',
							'responsive' => true,
						],
					],
				),
				version_compare( NEVE_VERSION, '2.6.3', '>=' ) ? 'Neve\Customizer\Controls\React\Responsive_Range' : 'Neve\Customizer\Controls\Responsive_Number'
			)
		);

		$rows    = get_theme_mod( 'woocommerce_catalog_rows' );
		$cols    = get_theme_mod( 'woocommerce_catalog_columns' );
		$default = ( $rows * $cols ) > 0 ? $rows * $cols : 12;
		$this->add_control(
			new Control(
				'neve_products_per_page',
				array(
					'default' => $default,
				),
				array(
					'label'       => esc_html__( 'Products per page', 'neve' ),
					'section'     => 'woocommerce_product_catalog',
					'priority'    => 120,
					'type'        => 'number',
					'input_attrs' => array(
						'min'  => 1,
						'step' => 1,
					),
				)
			)
		);

		$this->add_control(
			new Control(
				'neve_shop_pagination_type',
				array(
					'default'           => 'number',
					'sanitize_callback' => array( $this, 'sanitize_pagination_type' ),
				),
				array(
					'label'    => esc_html__( 'Pagination', 'neve' ),
					'section'  => 'woocommerce_product_catalog',
					'priority' => 130,
					'type'     => 'select',
					'choices'  => array(
						'number'   => esc_html__( 'Number', 'neve' ),
						'infinite' => esc_html__( 'Infinite Scroll', 'neve' ),
					),
				)
			)
		);

		$this->add_control(
			new Control(
				'neve_enable_product_filter',
				array(
					'sanitize_callback' => 'neve_sanitize_checkbox',
					'default'           => true,
				),
				array(
					'label'    => esc_html__( 'Products filtering', 'neve' ),
					'section'  => 'woocommerce_product_catalog',
					'type'     => 'neve_toggle_control',
					'priority' => 140,
				)
			)
		);

		$this->add_control(
			new Control(
				'neve_enable_product_layout_toggle',
				array(
					'sanitize_callback' => 'neve_sanitize_checkbox',
					'default'           => false,
				),
				array(
					'label'       => esc_html__( 'Layout toggle', 'neve' ),
					'section'     => 'woocommerce_product_catalog',
					'description' => apply_filters( 'neve_external_link', 'http://bit.ly/neve-woo-lt', __( 'View more details about this', 'neve' ) ),
					'type'        => 'neve_toggle_control',
					'priority'    => 150,
				)
			)
		);
	}

	/**
	 * Controls that refer to product card layout.
	 */
	private function add_product_card_layout_controls() {

		$this->add_control(
			new Control(
				'neve_product_card_layout',
				array(
					'transport'         => 'postMessage',
					'default'           => 'grid',
					'sanitize_callback' => array( $this, 'sanitize_shop_layout' ),
				),
				array(
					'label'    => esc_html__( 'Layout', 'neve' ),
					'section'  => 'woocommerce_product_catalog',
					'priority' => 210,
					'choices'  => array(
						'grid' => array(
							'name' => __( 'Grid', 'neve' ),
							'url'  => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAS8AAADYCAAAAACfVToRAAAEtElEQVR42u3cIXPjOhSG4f3/9KOGgoFmhmZlspvUabITsp5LjMyMzgWSHadNE7fJve1G74fSmbTgGUk+OpL7y8hn8gsCvPDCCy+8CF544YUXXngRvPDCCy+8yP/g9c/vbwteeOGFF1544YUXXnjhhRdeeOGFF1544YUXXnjhhRdeeOGFF1544YUXXnjhhRdeeP0Ur9dbsk/Ma9/U1U2pX1Py2j9XN6dJyOsOXFW1TcZrew+uqk7Ga3MXrwsD7MG87sN1YQV7SK96+9VqYpOk1/rL5egrXnjhhddP8drjtdxrv6mrqm72eC3y2sWd9/MeryVeU6Nig9cCr9leco/Xda/N5f3NS1Xv8Jp5rS96barqDFjKXi+X+g1h8L0DY/06v36Nc/UtWNLPx6nX+vJxm+wNWNJeYy9/c6mreAqWeH3fPFfVenu5CXsCxn77es96DobXghb/DAyvJSciRzC8Fh0gTWB47ep6e/28bQRL3mtXz8v7j48n6z1exxbY9vpp7itex45hALt4+I3XjKuqttfuCuA156qq7ZWrFXj9bj5zZQIvvPDCCy+8EvHarj+RHV7cz8ELL7zwwguv+fsK66+mTsyr5v2OT3nd6f2hXSpeu7twrdN5/7G5A1e9S+h95OY/5XrA9913m5sW/efL1/Ufz4v/p4AXXnjhhRdeeOGFF1544YUXXnjhhRdeeOGFF1544YUXXnjhhRdeeOGFF1544YUXXkl6dX++LX+l1yMGL7zwwgsvvAheeOGFF1543T9OMfnh+pe9lOM1przdq1zyVx7FSx6vJV65mVmTSSu8FnuZlzSY937ocpVmvZck34evNSvJNee9vJNUHhLzaiT1JqmRVFo7TtLRQZIyKTeT1JpZL6k36920/MVvKaHxFVBU9mE585K6YHkIau+8nFSYHTKpSckrrl+SVA5mRZxYpVSYufgsyN97NZIzM3uSigSfj4p8WZyIraShD6NsXO9PvArpKdF6ojQzSc1xrsUP7TjHmvdeLvxCel6hvj9dm/D6eL0PiRan83E4Ox87Sb3lcT723vuEvWbrfW62iuv9avTyUwHi43rv01nvz3l17+oJP9UTK8n11jlJvQ2xngi/WP6IDfk3eNnhXL0axlcTfghewW18XPh06tU3Xmf3Q733jZkdVpL8MD4Twn6onareh/Z6xOCFF1544YUXwQsvvPDC6+b4HC+8bkyzmvUhfCa5cC+gfZo3KPCKyWP7qrXj2avrx1bW2ADDK+ZpOh7qzVbjZ3fsF8YmF15mFg55ijCYnsa2cyHpUEiu83nvfkRH/sd4jccVhVRMhxyZ5J3kzefmpQyvKWU8DiulwlxcrJzkV9GL5+NJDt63I5F57/s4RzsvqcgdXh8NMzfEz8NKKqYHQeYHvN6ky0MJEUbceGLUxYoia/F6u+ariKNoKOb3WL3zkrIer1lFsZLcWJO2mZR38/3Q4BbcA07Iq8uOg2sswCzcBxjM52Y5XrMM2awebXW8nJTFeqLP8Dq7HZI7boekMtYTrF+nyWZeneZ3DfPpM8/HY2ZErj3xsiY0K8q/r6FDvxCvh/T6S4MXXnjhhRdeBC+88MILL7wIXnjhhdcj51987R/KzzMKWAAAAABJRU5ErkJggg==',
						),
						'list' => array(
							'name' => __( 'List', 'neve' ),
							'url'  => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAS8AAADYCAAAAACfVToRAAAE0UlEQVR42u3csVPqTBTG4fv/t2+bckvKdCnT2W2iGASH5ma+JlW6VOcrdkOCIKBBwJvfW+GMOvLM7ubsctY/Rr6SPxDghRdeeOFF8MILL7zwwovghRdeeOFF8MILL7zwwovghRdeeOGFF8ELL7zwwovghRdeeOGFF8ELL7zwwovg9RNe/73fLXjhhRdeeOGFF1544YUXXnjhhRdeeOGFF1544YUXXnjhhRdeeOGFF1544YUXXnjh9Sheb1OymZnXpiqLSSnf5uS1eS4mp5qR1xW4imL1bS+nmHR7/n16Kb2z1+oaXEU53UvKp3vll/yWSV7Lq3idGGCXe8n/Aq/rcJ1Ywc57pWZmVSItfo1XufpuNbG8kpd5SZ1577smVW7Weknybfi2aiG56riXd5Ly7S29Xr5djr5dy6uS1JqkSlJudT9JewdJSqTUTFJtZq2k1qx1u+Uvfpdm4RXGV0BR3oblzEtqguU2qB14OSkz2yZSNSevuH5JUt6ZZXFi5VJm5uKzID30qiRnZvYkZTOZj+PnoyJfEidiLalrwyjr1/s9r0x6uv16P/La3M0rNzNJ1TDX4ou6n2PVoZcLP3Anr82yLIqy2tzBK9T3+2vTo3ut4877eXNLr3GJEC3252N3dD42klpL43xsvfc399odVCzv7TVa71OzRVzvF72X3xUgPq73/g7r/WgvubmzV3NQT/hdPbGQXGuNk9RaF+uJ8IP5ZRvyK3ktT+9vXotyfSsv2x6rV8P4qsIXwSu49Y8Lf9P66+Wk17IojoD9lNfR/VDrfWVm24Uk3/XPhLAfqndV7828Xk+dN4TBdwA21esuucH61c/Vj2Bz9hrOWl8/4zoAm7VXf5a//JzrI9isvd431XNRvKxOcX0Am7fX8Xw8sx6D4XWWaw8Mr/NcYzC8LuAageG1LsvVOa4BbPZe63Jc3n/+8WS5wet9dwS2OsdVFG94DSeGAezkh994jbiKYnWuVwCvMVdRrM60VuD1Xn2lZQIvvPDCCy+8ZuK1evlC1nh9I3jhhddMvHyKF14TvarFqE/CJ5IL9xbqp3EDxWN5FS/fTTnVK43tNbUNveGu7Vtt+gadh/GaeJdv8v2Op137amu26F+7oZ8pNuE8iteV7g+tv+nVSsrCYHrq2+IySdtMco1PW3dZx+DNvNZX4Tqx/p1Z0WM7ZSZluybMRPJO8uZT81LySF5f2zOe+XDt6155bNfNpcxcXKyc5BfR69Gej9cAO8V1xmvrfd0Tmfe+jXO08ZKy1D2g1/t6OWnRfz7drn/RG8gl18XX3ULKdg+CxHeP5vWzueDPb9JQQoQR13e0NrGiSGq8Pq75yuIo6rLxPVvvvKSkxWtUUSwkt41f1ImUNoNkap274J7yjLyaZBhcfQFm4b5CZz41S/EapUtG9Wit4fJUEuuJNsFrlGE7JDdsh6Q81hOsX/tJRl6Nxnch091rno9DRkSu3vOyKhxW5D9woPOv/j+rh9wP4fXPeP1U8MILL7zwwgsvvPDCCy+88MILL7zwwgsvvPDCCy+88MILrwlezd+75Vd6EbzwwgsvvPAieOGFF154EbzwwgsvvPAieOGFF1544UXwwgsvvPAieOGFF1544UXwwgsvvPAieOGFF1544UXwmpz/AdpnH8pqwZaBAAAAAElFTkSuQmCC',
						),
					),
				),
				version_compare( NEVE_VERSION, '2.10.1', '<' ) ? 'Neve\Customizer\Controls\Radio_Image' : '\Neve\Customizer\Controls\React\Radio_Image'
			)
		);

		if ( ! neve_pro_is_new_skin() ) {
			$this->add_control(
				new Control(
					'neve_box_shadow_intensity',
					array(
						'sanitize_callback' => 'absint',
						'default'           => 0,
					),
					array(
						'label'       => esc_html__( 'Box shadow (px)', 'neve' ),
						'section'     => 'woocommerce_product_catalog',
						'type'        => 'neve_range_control',
						'step'        => 1,
						'input_attrs' => array(
							'min'        => 0,
							'max'        => 30,
							'defaultVal' => 0,
						),
						'priority'    => 220,
					),
					class_exists( 'Neve\Customizer\Controls\React\Range' ) ? 'Neve\Customizer\Controls\React\Range' : 'Neve\Customizer\Controls\Range'
				)
			);
		}

		$this->add_control(
			new Control(
				'neve_add_to_cart_display',
				array(
					'default'           => 'none',
					'sanitize_callback' => array( $this, 'sanitize_add_to_cart_display' ),
				),
				array(
					'label'    => esc_html__( 'Add to Cart Button', 'neve' ),
					'section'  => 'woocommerce_product_catalog',
					'priority' => 230,
					'choices'  => array(
						'none'     => array(
							'name' => __( 'None', 'neve' ),
							'url'  => 'data:image/png;base64,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',
						),
						'after'    => array(
							'name' => __( 'After Image', 'neve' ),
							'url'  => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAS8AAADYCAMAAACN4JX/AAAAq1BMVEU/xPtExftRyvtSyvtTyPtbzfxhzvxkzPxm0Pxv0/x00fyD1fyM3P2R2fyTk5OcnJye3f2kpKSn5P2q4f2srKyy5/2z6P20tLS25f27u7vB6f3Dw8PH7v7KysrK7/7M7P7R0dHS0tLT09PU1NTW1tbW8v7X8P7Y2NjZ2dna2trb29vb9P7c3Nzd3d3f39/i9P7l5eXs7Ozs+P7x8fHy8vL1+//5+fn+//////+ynh90AAAHOUlEQVR4AezQwQ1CMQxEQVrB/lJC5D1xof/KKAFyAGelNyXM7fWNp7bM+wke2vZ54idfFQd0hWy+NA74GkZfyvaulNPXiuauqN4vs7BY8vrSysauLLl9SSOatmJIhl+qef2/LK9Z6v/yxxdffPHFF1988cUXX9744osvvvgCX3zxxRdffIEvvvjiiy++wBdffPHFF/jiiy++3uzb0YrzKhAH8DcYEQIhSGDuvBMF8fj+T3ZIM50aE9d87MWSOv+r7XQzkB8q2qZjeUGVCX3+dSK88u1elDnkexEviv1bL7r6OV7g8r9EvFTMP0S8ZnrlDYGtuRPxIjEaYLkT8aLQCAv5VsTLXa34OAGYvCdsLwA0unyMW7e6cfe86mZP9aJ7tTzUYg4a4O0VZuDo8iYjvzHFwstQNwpQy3xutv8PVSj4RC8PwF4WKFyjBAUc7e95Vc2WJ3rxfOR7Depjg1BleSursqpvedlzs+d58XrPf87AXgGq8B3NVbnrddnMPsur3k+Yeu5pGgch57RSORZXwZpyDss9r7lohjQun71fNUwVygmEtGIVS9hy2ISsd7z8VbPw5PMQe/m8ZyFbCvJITNXs0X0vajblQ2/76PO2qRgU6VGSeo8Jt8ulTLF9L2rmvuK8Tbdnjgyhvo/5zYnVIhj7XtQsfYHXHHLptRx3GlPmrO+lztAfnK4XN3u0V/V5tDlOR1uNIhpWpvxHiu56UbNHnx8pv/ISL9ufj2tvPmLDa/pWr856X918aHkl9vJVs4Bb/Dd5JbjcTzi++ZgpWHutR8h4brae9l/p6V55vtyvJt5MmeZ+dTpCxqoZ28dy0j7ey0JZCOp0HrLn8xCW4ygqpqiarSUrHSyf7sXjxvARuT5vm1iftx0dsOxmp8prdHHJ0Rs4+GwvB6esfPw7hxe9OvGyGc9a9XwvCkKVufq8kLK/oiZlfS6WJmx96YnP92qALdXn0RTU5FVLOsNep2Y6cLP5a7x633fQFYVXDhooyueDV/YTcEyqv2r6Y6/fp/N9msf9AJpyxlcyxb6UZtwgcEsqrtH7e1Gel/ttxEu8xEu8JOIlXuIlXuIlES/xEi/xEi/xkoiXeImXeFkAn/884iVe4iVeSE+12Vv1RGWNbkgvBI5y3XpcoKjb4bzSBGWWTt3BMfNYXhULwbTrDurMY3mtUMf+UI8KTsGRvOLnSZtERCq16wZeWZMF8F5TfSCv3ULH6qe1rbqCLUj7iUgvx/EiAHt4Ts0064F+wUdepLqM40UAxx3E3Kx72KLZi37xMY6Xu/oFmm7WHTEOu7+3sGU9vpya9QCv4OBeeFz9l3Zd0V7MyXn7s7vCdt3AJ8aN7kVb+tiuJw1lFhzYK800bn6qBw3H4KBeLKHij/Vk4JgpDumFQPG9ekQFZXQczytMzHKnHnGBT+bhvCxQptCt8zvI48wP5rUAxdyqk5fnyboM5RU1qWjfrVdeBDaN5BUUsZh+fS9F9uID+Dhe8c3i+nXauAb2eh8ox/EiAtDhTn2lLSp72dHGFxJLvFV3750reSU92HqfaNaFm/W9rPzu5fVo+wmEq/Tr8wJgBtyvLnCVdp2WtWHPQwqu0q7ndALTIQ/jleAyzfoWc6yZlMfxinCZZv2VgMBZgzz/dSe4ABjMfxZ5Xk68xEu8JOIlXuIlXuIlXhLxEi/xEi/xkoiXeImXeImXeEnES7zES7zES7wk4iVeD/D67/927GBVahgK43jVaLS1UAxKKGJXduOiEAj83//JvHMzJ7VJYeCqlOj5Nu3JDGXyY05I+u1zQ/ny/Wqvj11b+XSx16vGvN5f7NU1lnfqpV7qpV7qpV7qpV7qpV7qpV7qpV7/r5dxEXD2OOo2wPf/ptcCMd1NpARnylLCnvWpHKUYuz19IGUSPiDhWSTeVg9rw8vs052QxL4uT7x6qMFsKAc3wBdeBNOmlwNYCi+ircoTrwDhyWTYnqd/zwy4++CtTqoxez1fN/Btem0QIDVg+t3GA3NV5qzSaZNAmihDiWRMg/cu9BDuY+LVOSGSh7Xi1cPqwYmXzCZWZe21gMt/0qXLt1u6G50bbtdInGAtvZYmvWZwPYSDlwH6sqy9AgxZXURnWask040mgi36cWjRy0QwMnEBShJlWXuRELJDBSAD403RHdf7qWvPS4g8zH/Ly0I03Qjh6OWa9FrBpXYyRT/aonxhP8pKFWHYXQfAtuZV9ccfXu9TDwYks3jJZ+15OXLWHcgDviof7Cfc+X5iICea3cuBb85LuiQ34GFuZVl7mbxf/fUbC6nHt2fuRSQ3mHavEebmvGQVltZ6fB4qpjienocikl6WPtmhZq+hvf2XrPbSWsEIUJTz9qGsvR6dt8ektPdpn70MRNOc1+/HOs7e5wTAW33/9fKol3qpl3qpl3qpl3qpl3qpl3qpl3qpl3q9aczrw8VeX183xfX2x6VemgdeGvVSL/VSL/XS/ASH6S8TuuP7JQAAAABJRU5ErkJggg==',
						),
						'on_image' => array(
							'name' => __( 'Over Image', 'neve' ),
							'url'  => 'data:image/png;base64,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',
						),
					),
				),
				version_compare( NEVE_VERSION, '2.10.1', '<' ) ? 'Neve\Customizer\Controls\Radio_Image' : '\Neve\Customizer\Controls\React\Radio_Image'
			)
		);
	}

	/**
	 * Controls that refer to category card layout.
	 */
	private function add_category_card_layout_controls() {
		$is_new_skin = neve_pro_is_new_skin();
		$choices     = array(
			'default' => array(
				'name' => $is_new_skin ? 'Simple' : '',
				'url'  => $is_new_skin ? 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iOTAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA5MCA2MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjkwIiBoZWlnaHQ9IjYwIiByeD0iMSIgZmlsbD0iI0YzRjRGNSIvPgo8cmVjdCB3aWR0aD0iNzEuNzUiIGhlaWdodD0iNDEuODM3OSIgdHJhbnNmb3JtPSJtYXRyaXgoLTEgMCAwIDEgODAgMCkiIGZpbGw9IiNEREREREQiLz4KPHBhdGggZD0iTTIzLjU2NjQgNTBINTEuMTk2NCIgc3Ryb2tlPSIjQzRDNEM0IiBzdHJva2Utd2lkdGg9IjIiLz4KPHBhdGggZD0iTTU1LjMxNjQgNTBINjQuNjg1NSIgc3Ryb2tlPSIjQzRDNEM0IiBzdHJva2Utd2lkdGg9IjIiLz4KPC9zdmc+Cg==' : 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAS8AAADYCAYAAAC6PmXNAAANMklEQVR4nO3dx2sU/x/H8Y81UVC/Yu8FO9gQCyiCoCCoFw+CePDg1T/HkzevCiKiKOhBxIsNG4gNe+8FicaSH6/55b18dnZms5tk4753ng8IxpTN7K7zzOfzmdlxUFdXV1cAAGcG84QB8Ih4AXCJeAFwiXgBcGkoT1u2X79+hc7OzmbcNBTIkCFDQnt7O095BuKV4/v37+Hjx49NuW0ojpEjR4bJkyfzjGdg2gjAJeIFwCXiBcAl4gXAJeIFwCXiBcAl4gXAJeIFwCXiBcAl4gXAJeIFwCXiBcAl4gXAJeIFwCXiBcAl4gXAJeIFwCXiBcAl4gXAJeIFwCXiBcAl4gXAJeIFwCXiBcAl4gXAJeIFwCXiBcAl4gXAJeIFwCXiBcAl4gXAJeIFwCXiBcAl4gXAJeIFwCXiBcAl4gXAJeIFwCXiBcAl4gXAJeIFwCXiBcAl4gXAJeIFwCXiBcAl4gXAJeIFwCXiBcAl4gXAJeIFwCXiBcAl4gXAJeIFwCXiBcAl4gXAJeIFwKWhPG3F8+fPn8Ld50GDBoXBg/ld3UqIV0F0dXWFX79+JW96v4gUsPb29jBkyJCi/3NoCfwqKgDFqqOjI3R2dhY2XCH1OMA/4lUA2mH//v1b9IehRPH6/ft3k2wNeot4tThNEwlXpZ8/fzbbJqFOxKvFFXFxvhaaQjL68o14tTh20HyMSH3jaGNB6chbW1tb8mcrU7w1dUbrIV4FpXOehg4txtNPvFoT00YALhEvAC4RLwAuES/0qMhn5aN5sWCPTAqWnYlu8dKRyWHDhiVvrX6UEs2PeKGCTmz98eNHxYgrDtqIESMIGP4ppo2okBWumE7u5MXN+NeIF8rUeskcXjOJf41pI8rUEyRNH4cPH95vD6BeLK0oct0t1IKRF8r8q9GUhSt0r61p6sqLylEN8UKZei6V3F8L9nG4DAFDT4gXytQzVeuPaV1WuAwBQzXEC2X0Yu1aRl8616uv/6FFtXAZAoY8xAsVdA5XtTApcLqcTl/UEi5DwJCFeKGC1rIUMB1JjCOmaaKipSOBfVFPuAwBQxqnSiCTAqZ49eepEKGX4TIWME6jQGDkhYHUl3AZRmAwxAsDoj/CZQgYAvHCQOjPcBkCBuKFmigS379/T97qCVEjwmUIWLERL/QovkSO3moNUiPDZSxgvEi8eIgXqsq7tldPYRqIcBmLKoqFeCFXXrhMXqAGMlwoLuKFTD2Fy6RDRbgwUDhJFRVqDZdRsEL35XQIFwYK8UIFxaveNSQLGDBQmDYCcIl4AXCJeAFwiXgBcIl4AXCJo42ooGt5cb0sNDvihQq6Pr3egGbGtBGAS8QLgEvEC4BLxAuAS8QLgEscbSwovfi6o6Oj5e88V1htXcSrxemcrbwrRHDtd3jGtLHF6b/mRzZOxPWNeLU44pVN4SJevhGvFqcdtL//y37vNJXmMfGPeBWAdlR21v9TuNrb2xl1tQDmFAWheGmH/f37d/JWtP8qbPDgwckUWq/ZVMDgH/EqEFvnaWtrK/pDgRbAtBGAS8QLgEvEC4BLxAuAS8QLgEvEC4BLxAuAS8QLgEvEC4BLxAuAS8QLgEvEC4BLxAuAS8QLgEvEC4BLxAuAS8QLgEvEC4BLxAuAS8QLgEvEC4BLxAuAS8QLgEvEC4BLxAuAS8QLgEvEC4BLxAuAS8QLgEvEC4BLxAuAS8QLgEvEC4BLxAuAS8QLgEvEC4BLxAuAS8QLgEvEC4BLxAuAS8QLgEvEC4BLxAuAS8QLgEvEC4BLxAuAS8QLgEvEC4BLQ3nasg0fPjyMHj26GTcNBaJ/h8g2qKurq4vHBoA3TBsBuES8ALhEvAC4RLwAuES8ALhEvAC4RLwAuES8ALhEvAC4RLwAuES8ALhEvAC4xFUl6vDt27fw6NGjcOvWrfDp06fSN86ZMyfMmDEjLFu2zMG98O/ChQvh+vXryf3YuXNnmDp1as33qbOzMxw5ciR5/saOHRv27NlT9IfTLa4qUaObN2+G8+fPV/1i7QybNm2qa2fKE++gK1asCBs2bGjsHXTi5cuX4ejRo8nG6pfGtm3bMjc8fr7Sgbt79244c+ZM8v7GjRv5peMU08YaKCQ9hUv021w71ocPH5pgq1vT7du3S/dr5cqVmfdRoyuNjvMoeiNGjEg+e/ny5aI/pG4xbeyBfkvbCEj0j16joIULFyZ/145y6dKlsq85ffo005EG0GN9586d5Ib1PGSNcDXiSk/r03SBv1mzZiW31dHRkTzH9nzCD+LVgytXrpS+QDvMrl27wqhRo0of046gmLW1tYWLFy8mH9OOo+lNvHPp7w8fPgxv3rwJr169Sj42ZcqUMGnSpDB37txeTTVtZ9boQTth6J66Ll26NHcqpO24du1asnZnNHXSaOTQoUOl28iKb1YYNKXN2n4F4efPn8n706ZNSx4zi3xv15osXKL4xA4cOFDXbc2bN690e8+ePSNeDhGvKh4/fly2o65evbosXLHly5cnAcsSr1/FFDG96XNbtmxJdqCsr9Xf7WP79+9P/tTU9NixY6VoGW2vprj3798PO3bsKLuMcLzWE7Ovz6MDFcePH88czdi2rV27Nnl8zIMHD0qBVBzj6OnPODbbt28Ps2fPLrvdkydPlr5/0aJFYfPmzeHt27elz0+cOLGep7LCuHHjSh9SxLSNXHLZF+JVxdevX8s+qRFEHv3DzxrtKIBxjLSTjB8/Pnk/HgEpWrX+9ldM4nBZ+OTs2bPJzqgoKkra6e174nDFsenpYEQcLgtJ6B7FnTp1KtkOjToV76zHIB4Zhu6R3X///Ve678+fPy+Ll0aU8chwyZIlyZ9PnjwpfSz9XFjUTU8jMf0S0kjatuv9+/f9cqAFA4d4VZGOV/zbulbaMY2mWOmd23ZS24k0BdVbtaONtlYTumMYR09h0dRUsdHXacfXThlPuTRFjEdJ2ibd16zRocJm4VJ0LFyi29V2WRQVqax4aVv1M9etW1d6DDUKtPuu99esWVMa+cTbqqm1fo6CFgcwbwRcDwWUePnF0cYG086tUYHe4gBpZ3zx4kWvfriCYmwUF1uwYEHpb/Yznj59WvqYzklLmz59eubPiqdq8e2aOJwKgUaaaRrhaHQYxz8+4qfv0yjOaA3KzJ8/P3lPcYn1xxSvvb29z7eBf4eRVxXpNSytM1UbfcVR0X+bZlMhmwa9e/cufPnyJbx+/bpirapW6RGInfOUx+Lz48eP0ldkrc3lxUCjuPg+ZdGIzEZntkgfU+DSt2/TbDvIoTUyPV7pKaOmqY0yZsyY0i2nR9lofsSrigkTJpR9UqOYvHhpp4vXjTSdCzkL6xpxaOShiMRTpEaKRxlZgdH2N0pe9HSU0uJli+bx46HpskWvkdsHn4hXFVoDiRd1taaj6GStt9y4caPs77agfO7cudL363u1g9r3a6pUb7y0M8fbtHv37prW4mbOnFk6RUPTsvTaVLw2F9OpHDaqyhudxEch8464ZtF26zGxkZYei/iop+IW3+/+Fo9G+Q+G/WHNqwrtMPHCtoJx+PDhZIE5psV1G0GE7khZUCwYofuM8Dh8vR1NxOtMWetmmr7qaJvebCobH51TLOIzy/U1WYv1IXVKwr179yo+Hz8WeSeOVqPzrYzCZY+XpqLxbaV/YejoaV9pCg+/GHn1QCMUrRvZCEkB09G1rPOlQvcObFPGtPiIlsJ19erVmrYhvZPpnDKLjSIUr6/Fpz0oADbC0s/V6REWWf0ZB1dH9eLQGq052TlaetOpGPGpEgq3UejrHSHZwr0e1/jn60TbWPrUBsWrr0ccP3/+XHo/68AHmhvxqoF2Vi3uxjt7FsVi69atZTuV1m0sNIqKQhCf45Qnnsboa+28JR211O3rxcZarNfOfOLEiYpb0Y6ubYkpLlrHUzQtFPo6fTw+wz6mGOl29JInO/0ia6qrMPbmBc66fY0k0yO/rIV6e0lP6IdTGxS/eB2SePlDvGqkHVw7VL2XxNH5Sxo5WaxsBGNXoLCTPEP3SMZ2SLutvJNH9XV79+5Nbjf+GouRtjVrFKQRWvpsdvvZeTQF1st56nl5UD0WL15cFq94oT6mKazFKz6FozfiF8/r+ePsen+4JM4A0W96W6fRyClryqOpZKN2ong6qcDt27cv9/PxWfQDQY9LPOrLu0aXHp+DBw/m3od62CsRQuoVCvCDBfsBolhph9Rb3lpNI3/7a3RhNNKL16o06ooX8LNOYu1vCpaCmX5pUnqhPqbHR6OykHFiaz0UQXupkZ22An+YNhaEgqkRhh1oiF/sHUu/3KhRFK+sKfH69eur/sT4YIVeG9qb6aqm2jZV781BBjQH4lUgipIWpnVpnvTBBy2463SKf/X6Ph3tXLVqVeZ6XEwRtqOmilD60kM90ajLLnOkUV4jz+BHY7HmBcAl1rwAuES8ALhEvAC4RLwAuES8ALhEvAC4RLwAuES8ALhEvAC4RLwAuES8ALhEvAC4RLwAuES8ALhEvAD4E0L4H3x5LwD/teG1AAAAAElFTkSuQmCC',
			),
			'style-3' => array(
				'name' => $is_new_skin ? 'Boxed' : '',
				'url'  => $is_new_skin ? 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iOTEiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA5MSA2MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3QgeD0iMC4xODU1NDciIHdpZHRoPSI5MCIgaGVpZ2h0PSI2MCIgcng9IjEiIGZpbGw9IiNGM0Y0RjUiLz4KPHJlY3Qgd2lkdGg9IjcxLjc1IiBoZWlnaHQ9IjUwIiB0cmFuc2Zvcm09Im1hdHJpeCgtMSAwIDAgMSA4MC4xODU1IDApIiBmaWxsPSIjREREREREIi8+CjxyZWN0IHg9IjI1LjY4NTUiIHk9IjEzLjgzNzkiIHdpZHRoPSIzOSIgaGVpZ2h0PSIyMS4xNjIxIiBmaWxsPSJ3aGl0ZSIvPgo8cGF0aCBkPSJNMzAuMzE0NSAyNEw0OS4zMDk0IDI0LjI2NDIiIHN0cm9rZT0iIzk5OTk5OSIgc3Ryb2tlLXdpZHRoPSIyIi8+CjxwYXRoIGQ9Ik01Mi4wNjA1IDI0LjMxODRMNTguOTM2NSAyNC40MTkiIHN0cm9rZT0iIzk5OTk5OSIgc3Ryb2tlLXdpZHRoPSIyIi8+Cjwvc3ZnPgo=' : 'data:image/png;base64,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',
			),
			'style-2' => array(
				'name' => $is_new_skin ? 'hover' : '',
				'url'  => $is_new_skin ? 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iOTEiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA5MSA2MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3QgeD0iMC44NjkxNDEiIHdpZHRoPSI5MCIgaGVpZ2h0PSI2MCIgcng9IjEiIGZpbGw9IiNGM0Y0RjUiLz4KPHJlY3Qgd2lkdGg9IjcxLjc1IiBoZWlnaHQ9IjUwIiB0cmFuc2Zvcm09Im1hdHJpeCgtMSAwIDAgMSA4MC44NjkxIDApIiBmaWxsPSIjOTk5OTk5Ii8+CjxwYXRoIGQ9Ik0yNC4xODE2IDI0SDUxLjgwNjYiIHN0cm9rZT0id2hpdGUiIHN0cm9rZS13aWR0aD0iMiIvPgo8cGF0aCBkPSJNNTUuODA2NiAyNEw2NS44MDY2IDI0IiBzdHJva2U9IndoaXRlIiBzdHJva2Utd2lkdGg9IjIiLz4KPC9zdmc+Cg==' : 'data:image/png;base64,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',
			),
		);
		// If is not new skin add legacy choices
		if ( ! $is_new_skin ) {
			unset( $choices['default']['name'] );
			unset( $choices['style-3']['name'] );
			unset( $choices['style-2']['name'] );
			$choices = array_merge(
				$choices,
				array(
					'style-1' => array(
						'url' => 'data:image/png;base64,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',
					),
					'style-4' => array(
						'url' => 'data:image/png;base64,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',
					),
					'style-5' => array(
						'url' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAS8AAADYCAYAAAC6PmXNAAAN/0lEQVR4nO3dyY8U5R/H8YdhYAaMiKCCsgnKIkHR4IYQI1vihYRwIxw8ePXP4eSNKyGEg1GIGJdwEJQI7gsogmwqiCjMMCDzy6d+/a08XV3VdM90F/Pter+SzjgzvVRXU+9+nqqadtLo6OhoAABn+njBAHhEvAC4RLwAuES8ALhEvAC4RLwAuES8ALhEvAC4RLwAuES8ALhEvAC4RLwAuES8ALhEvAC4RLwAuES8ALhEvAC4RLwAuES8ALhEvAC4RLwAuNTPy5ZvZGQkDA8PT8RFQ4X09/eH6dOn85LnIF4Fbty4Ea5cuTIhlw3VoXARr3xMGwG4RLwAuES8ALhEvAC4RLwAuES8ALhEvAC4RLwAuES8ALhEvAC4RLwAuES8ALhEvAC4RLwAuES8ALhEvAC4RLwAuES8ALhEvAC4RLwAuES8ALhEvAC4RLwAuES8ALhEvAC4RLwAuES8ALhEvAC4RLwAuES8ALhEvAC4RLwAuES8ALhEvAC4RLwAuES8ALhEvAC4RLwAuES8ALhEvAC4RLwAuES8ALhEvAC4RLwAuES8ALhEvAC4RLwAuES8ALhEvAC4RLwAuES8ALhEvAC4RLwAuES8ALhEvAC4RLwAuNTPy1Y9//33X+We86RJk0JfH+/VvYR4VcTo6Gi4detWctF/V5ECNjg4GCZPnlz1fw49gbeiClCshoaGwsjISGXDFTLrAf4RrwrQBnvnzp2qr4aU4nX79u0JsjQYK+LV4zRNJFyNbt68OdEWCW0iXj2uijvnW6EpJKMv34hXj2MDLcaI1DeONlaUjrwNDAwkX3uZ4q2pM3oP8aoonfPU31+Nl5949SamjQBcIl4AXCJeAFwiXrirKp+Vj4mLHfbIpWDZmegWLx2ZnDJlSnLp9aOUmPiIFxroxNbh4eGGEVcctGnTphEw3FNMG9EgL1wxndzJHzfjXiNeqNPqR+bwN5O415g2ok47QdL0cerUqR1bgfpjaUWRz91CKxh5oc69Gk1ZuEJt35qmrvxROZohXqjTzkcld2qHfRwuQ8BwN8QLddqZqnViWpcXLkPA0AzxQh39sXYroy+d6zXe/6FFs3AZAoYixAsNdA5XszApcPo4nfFoJVyGgCEP8UID7ctSwHQkMY6YpomKlo4Ejkc74TIEDFmcKoFcCpji1clTIcIYw2UsYJxGgcDIC2UaT7gMIzAY4oVSdCJchoAhEC+UoZPhMgQMxAstUSSuX7+eXNoJUTfCZQhYtREv3FX8ETm6tBqkbobLWMD4I/HqIV5oquizve4WpjLCZSyqqBbihUJF4TJFgSozXKgu4oVcdwuXyYaKcKEsnKSKBq2GyyhYofZxOoQLZSFeaKB4tbsPyQIGlIVpIwCXiBcAl4gXAJeIFwCXiBcAlzjaiAb6LC8+LwsTHfFCA30+vS7ARMa0EYBLxAuAS8QLgEvEC4BLxAuASxxtrCj98fXQ0FDPP3k+YbV3Ea8ep3O2ij4hgs9+h2dMG3uc/tf8yMeJuL4Rrx5HvPIpXMTLN+LV47SBdvp/2e+dptKsE/+IVwVoQ2Vj/T+Fa3BwkFFXD2BOURGKlzbY27dvJ5eq/a/C+vr6kim0/mZTAYN/xKtCbD/PwMBA1VcFegDTRgAuES8ALhEvAC4RLwAuES8ALhEvAC4RLwAuES8ALhEvAC4RLwAuES8ALhEvAC4RLwAuES8ALhEvAC4RLwAuES8ALhEvAC4RLwAuES8ALhEvAC4RLwAuES8ALhEvAC4RLwAuES8ALhEvAC4RLwAuES8ALhEvAC4RLwAuES8ALhEvAC4RLwAuES8ALvXzsuWbPn16mDx58kRcNFTI9evXebkLMPIC4BLxAuAS8QLgEvEC4BLxAuASRxtLpqNHZ86cCT/88EP4+++/0wefP39+eOyxx8JTTz1VkTVxb3322Wfh22+/TZbh9ddfD3PmzGl5eUZGRsK7776bvH4PPPBA2LZtWy+uogmPeJXou+++C0ePHs19wN9++y25KGpr165ta2MqEm+gK1euDC+88IKfldVFly5dSteL3jSK1nX8esWBmzp1anj66afD4cOHk4DperzplI9pY0kUkqJwxbQxHDhwIPz111+9tQImkJ9++ildmFWrVuUumEZXeiMpsmDBgjAwMJD89sSJE1VafRMGI68SnDp1Kn2nF/2j1yjoiSeeSL7XhqINIL7Oxx9/zHSkC7Su9XqE2uuQN+rSSCo7rc/S6EujNt3XzZs3k6/2eqIcxKsEX331Vfog2mC2bt0a7rvvvvRn2hAUM309fvx48jNtOJrexBuXvtf+sj///DP8/vvvyc8eeeSR8NBDD4WFCxeOaappG7PiqY1QtB9n+fLlhVMhLcfXX3+dTHPNiy++mCzD3r170/vIi29eGDSlzVt+LZeWT+bOnZusM4v8WPc1WbhCbcoY2717d1v3tWjRovT+Lly4QLxKRry67OzZs3Ub6urVq+vCFVMsFLA88f6rmCKmi363fv36ZAPKu66+t5+98cYbyVdNTQ8ePJhGy2h5NcU9ffp02LRpU90yaWPVvp4su34RHah4//33c0cztmzPPvtssn6M7s8CqTjG0dPXODYbN25MpnKxDz74IL291ovWz+XLl9NrzJ49e1wv/qxZs+rWi5ax6PVD5xGvLvv333/rHkAjiCL6h5832lEA4xhpI7ENJx4BKVqtvvsrJnG4LHyiOGljVBQVJf3ObhOHK45Ns4MREofLQhJqo7gPP/wwWQ6NOovWQTwyDLWR3f33358+94sXL9bFSyO2eGS4dOnS5Gv8s+xrYVE3dxuJ6U1II2lbLr0ZdOJAC1pDvAr09fWFKVOmjPt+bty4Ufe9pnntsiliqO1gfuaZZ9Lv+/v70w1SG5GW+ZVXXkkun376aRI3u93LL7+c3u6XX35JNzod3VyxYkX6uw0bNiRTU8VGEVNMtKHrNkbTvOeffz79Xsuk52qPJ7b+vvnmmzRcio7u32jqpsf/6KOPkp98+eWX6fPTa2C0rPaYFu6TJ0+mz/3nn39Ofmcjnx9//DG9rYKix1HQ4gDOnDmz5ddY6znvuroPBTjURoPZqeh46QMCUPCasF7y6R+rLp24n9jg4GDb9/Haa68llyxtjH/88Ufh/cePrf+OfxeP5B599NGG5VLMjhw5kvy3HuPxxx8P58+fT3+v77O30c8sXgqP/T4+cqr7zd5OYbV4DQ8PJ6Mo3Vf8qR7Tpk1LTleIp2XLli1LlnFoaCi53ZUrV5LbhdpIzGj/nR5Tv4/NmDGjaJU30OPmvXZxXBS3sby+zXT6/noJ8eoyO5xutM+l2b4WjTyMNi7bGBUqjXwUEr3Da+PURjsWuq/4tvv27Wt6LzbyUyBM9nmF2gaex0YmoUkwHnzwwTRy2X1woRag7P3re43SLLIaiWl92boy8aiy0zSSNNeuXeva46AR8eqyhx9+uO4Bzp07VxgvbXSffPJJ+v2rr76afFXw9u/fXxccjUQWL16cROT7778v5bnEo4C8wNiRwW4oit6SJUvSeGk9aJ3F60P75Sx63Vw+lI94dZn+5EehsfBop7qio53NWdmTHefNm5d81ZTKbq/bagO122sq1268tDHHy7Rjx46Wjrxpn5NOCQi1gwjxvreQ2Rke0z4nG1UVjU7iqWXeqK6IllvrxEZaWhfxSaiKW/y8Oy0ejbYzDcX4cYZ9l9k5XEbB2LNnT8PZ2zqKZyOIUIuUBcWCIc8991xd+MY6mtA0zGg0mKXp665du5KLTWUtpqG2w18hNrqOnaOWFR+kiHekm3hdKKoKfjuefPLJ9NoKl60vTUXj+8q+Yfzzzz9jWnexZieyorsYeZVAIxTtN7IRkgKmUwd0yaMN2KaMWToKaBukwnXs2LGWnkB2I9MpDhYbRSjev6YQ2fRVAbARlh73pZdeSiOrr3FwteM/Dq3RPiedqKvRlS6HDh0KmzdvTn6rkWN8+oWdrNsOhd5GkvHj6+8PY4pXPOJUvPJGwO24evVqem2dLIzyEK+SaGPVzt14Y8+jWOioWrxRab+NhUZRUQh0iD7eKZ0nnsbouhpFyVtvvZXc//bt25Od9dqY33nnnYZ7sCN8McVF+/EUTQuFrqefKyJ550YpRrof+5tNRTxvqqswZqeirdD9aySZHfnl7ajXWfH22PEbwVgofvF+SOJVLuJVIm3g2qAUEhuJGG34Oskyb+PVSakaOVmsbASj0Omcqffeey/diDSSsQ3S7is+CBDT9XRipu43vo7FSMuaNwrSCM1GabH4VIosTYF37tyZjOqyz11x1r6p8YRE56LF8Yp31Mc0hbV4xefPjUV8tr5eP86uL9ek0dHR0So9Yc/0Tm/7aTRyypvyaCrZrY0onk4qcG+++Wbh7xU+mxqWQeslHvVpVJkXQ62ft99+u/A5tEPTXwvhli1b6vYjovvYYe+IYqUNUpeifTXdfPfX6MJopBfvq9KoK96Bn/07w25QsBTMOJohZ0d9TOtHo7JQew7NRovNKIK//vprcg07bQXlYtqIlimYGmHYgQZN0/KOMOpgQxmjEMUrb0q8bt26preLD1Z88cUXY5quaqptU/WxHGTA+BEvtEVR0o5p/S1h9uCDdrjrdIrx7LsaDx3tXLNmTe7+uJgibEdNFaF4P2ErNOr6/PPPk2tqlNfNM/hRjH1eAFxinxcAl4gXAJeIFwCXiBcAl4gXAJeIFwCXiBcAl4gXAJeIFwCXiBcAl4gXAJeIFwCXiBcAl4gXAJeIFwCXiBcAl4gXAJeIFwCXiBcAf0II/wM0p0Sfl+nB7gAAAABJRU5ErkJggg==',
					),
					'style-6' => array(
						'url' => 'data:image/png;base64,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',
					),
				)
			);
			ksort( $choices );
		}
		$this->add_control(
			new Control(
				'neve_category_card_layout',
				array(
					'default'           => 'default',
					'sanitize_callback' => array( $this, 'sanitize_category_card_layout' ),
				),
				array(
					'label'    => esc_html__( 'Layout', 'neve' ),
					'section'  => 'woocommerce_product_catalog',
					'priority' => 310,
					'choices'  => $choices,
				),
				version_compare( NEVE_VERSION, '2.10.1', '<' ) ? 'Neve\Customizer\Controls\Radio_Image' : '\Neve\Customizer\Controls\React\Radio_Image'
			)
		);
	}

	/**
	 * Controls that change the way that product image looks.
	 */
	private function add_card_image_controls() {

		$this->add_control(
			new Control(
				'neve_force_same_image_height',
				array(
					'sanitize_callback' => 'neve_sanitize_checkbox',
					'default'           => false,
				),
				array(
					'label'    => esc_html__( 'Force Same Image Height', 'neve' ),
					'section'  => 'woocommerce_product_catalog',
					'type'     => 'neve_toggle_control',
					'priority' => 410,
				)
			)
		);

		$this->add_control(
			new Control(
				'neve_image_height',
				array(
					'sanitize_callback' => 'absint',
					'default'           => 230,
				),
				array(
					'label'           => esc_html__( 'Image height (px)', 'neve' ),
					'section'         => 'woocommerce_product_catalog',
					'step'            => 1,
					'input_attrs'     => array(
						'min'        => 100,
						'max'        => 500,
						'defaultVal' => 230,
					),
					'priority'        => 420,
					'active_callback' => array( $this, 'same_image_height_active_callback' ),
				),
				class_exists( 'Neve\Customizer\Controls\React\Range' ) ? 'Neve\Customizer\Controls\React\Range' : 'Neve\Customizer\Controls\Range'
			)
		);

		$this->add_control(
			new Control(
				'neve_image_hover',
				array(
					'default'           => 'none',
					'sanitize_callback' => array( $this, 'sanitize_image_hover' ),
				),
				array(
					'label'       => esc_html__( 'Image style', 'neve' ),
					'section'     => 'woocommerce_product_catalog',
					'priority'    => 430,
					'description' => __( 'Select a hover effect for the product images.', 'neve' ),
					'type'        => 'select',
					'choices'     => array(
						'none'      => esc_html__( 'None', 'neve' ),
						'zoom'      => esc_html__( 'Zoom', 'neve' ),
						'swipe'     => esc_html__( 'Swipe Next Image', 'neve' ),
						'blur'      => esc_html__( 'Blur', 'neve' ),
						'fadein'    => esc_html__( 'Fade In', 'neve' ),
						'fadeout'   => esc_html__( 'Fade Out', 'neve' ),
						'glow'      => esc_html__( 'Glow', 'neve' ),
						'colorize'  => esc_html__( 'Colorize', 'neve' ),
						'grayscale' => esc_html__( 'Grayscale', 'neve' ),
					),
				)
			)
		);
	}

	/**
	 * Controls for card content.
	 */
	private function add_card_content_controls() {

		$order_default_components = array(
			'title',
			'reviews',
			'price',
		);

		$content_align = get_theme_mod( 'neve_product_content_alignment', 'left' );
		$components    = array(
			'category'          => __( 'Category', 'neve' ),
			'title'             => $content_align === 'inline' ? __( 'Title + Price', 'neve' ) : __( 'Title', 'neve' ),
			'short-description' => __( 'Short description', 'neve' ),
			'reviews'           => __( 'Reviews', 'neve' ),
			'price'             => __( 'Price', 'neve' ),
		);

		if ( $content_align === 'inline' ) {
			unset( $components['price'] );
		}

		$this->add_control(
			new Control(
				'neve_layout_product_elements_order',
				array(
					'sanitize_callback' => array( $this, 'sanitize_product_elements_ordering' ),
					'default'           => wp_json_encode( $order_default_components ),
				),
				array(
					'label'      => esc_html__( 'Elements Order', 'neve' ),
					'section'    => 'woocommerce_product_catalog',
					'components' => $components,
					'priority'   => 510,
				),
				'Neve\Customizer\Controls\React\Ordering'
			)
		);

		$this->add_control(
			new Control(
				'neve_product_content_alignment',
				array(
					'default'           => 'left',
					'sanitize_callback' => array( $this, 'sanitize_product_content_alignment' ),
				),
				array(
					'label'    => esc_html__( 'Alignment', 'neve' ),
					'section'  => 'woocommerce_product_catalog',
					'priority' => 520,
					'choices'  => array(
						'left'   => array(
							'name' => __( 'Left', 'neve' ),
							'url'  => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAS8AAADYCAAAAACfVToRAAAEtElEQVR42u3cIXPjOhSG4f3/9KOGgoFmhmZlspvUabITsp5LjMyMzgWSHadNE7fJve1G74fSmbTgGUk+OpL7y8hn8gsCvPDCCy+8CF544YUXXngRvPDCCy+8yP/g9c/vbwteeOGFF1544YUXXnjhhRdeeOGFF1544YUXXnjhhRdeeOGFF1544YUXXnjhhRdeeP0Ur9dbsk/Ma9/U1U2pX1Py2j9XN6dJyOsOXFW1TcZrew+uqk7Ga3MXrwsD7MG87sN1YQV7SK96+9VqYpOk1/rL5egrXnjhhddP8drjtdxrv6mrqm72eC3y2sWd9/MeryVeU6Nig9cCr9leco/Xda/N5f3NS1Xv8Jp5rS96barqDFjKXi+X+g1h8L0DY/06v36Nc/UtWNLPx6nX+vJxm+wNWNJeYy9/c6mreAqWeH3fPFfVenu5CXsCxn77es96DobXghb/DAyvJSciRzC8Fh0gTWB47ep6e/28bQRL3mtXz8v7j48n6z1exxbY9vpp7itex45hALt4+I3XjKuqttfuCuA156qq7ZWrFXj9bj5zZQIvvPDCCy+8EvHarj+RHV7cz8ELL7zwwguv+fsK66+mTsyr5v2OT3nd6f2hXSpeu7twrdN5/7G5A1e9S+h95OY/5XrA9913m5sW/efL1/Ufz4v/p4AXXnjhhRdeeOGFF1544YUXXnjhhRdeeOGFF1544YUXXnjhhRdeeOGFF1544YUXXkl6dX++LX+l1yMGL7zwwgsvvAheeOGFF1543T9OMfnh+pe9lOM1przdq1zyVx7FSx6vJV65mVmTSSu8FnuZlzSY937ocpVmvZck34evNSvJNee9vJNUHhLzaiT1JqmRVFo7TtLRQZIyKTeT1JpZL6k36920/MVvKaHxFVBU9mE585K6YHkIau+8nFSYHTKpSckrrl+SVA5mRZxYpVSYufgsyN97NZIzM3uSigSfj4p8WZyIraShD6NsXO9PvArpKdF6ojQzSc1xrsUP7TjHmvdeLvxCel6hvj9dm/D6eL0PiRan83E4Ox87Sb3lcT723vuEvWbrfW62iuv9avTyUwHi43rv01nvz3l17+oJP9UTK8n11jlJvQ2xngi/WP6IDfk3eNnhXL0axlcTfghewW18XPh06tU3Xmf3Q733jZkdVpL8MD4Twn6onareh/Z6xOCFF1544YUXwQsvvPDC6+b4HC+8bkyzmvUhfCa5cC+gfZo3KPCKyWP7qrXj2avrx1bW2ADDK+ZpOh7qzVbjZ3fsF8YmF15mFg55ijCYnsa2cyHpUEiu83nvfkRH/sd4jccVhVRMhxyZ5J3kzefmpQyvKWU8DiulwlxcrJzkV9GL5+NJDt63I5F57/s4RzsvqcgdXh8NMzfEz8NKKqYHQeYHvN6ky0MJEUbceGLUxYoia/F6u+ariKNoKOb3WL3zkrIer1lFsZLcWJO2mZR38/3Q4BbcA07Iq8uOg2sswCzcBxjM52Y5XrMM2awebXW8nJTFeqLP8Dq7HZI7boekMtYTrF+nyWZeneZ3DfPpM8/HY2ZErj3xsiY0K8q/r6FDvxCvh/T6S4MXXnjhhRdeBC+88MILL7wIXnjhhdcj51987R/KzzMKWAAAAABJRU5ErkJggg==',
						),
						'center' => array(
							'name' => __( 'Center', 'neve' ),
							'url'  => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAS8AAADYCAAAAACfVToRAAAEtElEQVR42u3cLXPjPBSG4f3/9KGGgoFmhmZlspvUabITsp6XGJkZnRdIdpw2X91ku9noflA6kxZcI8lHR3J/GPlKfkCAF1544YUXwQsvvPDCCy+CF1544YUX+fNe//38a8ELL7zwwgsvvPDCCy+88MILL7zwwgsvvPDCCy+88MILL7zwwgsvvPDCCy+88MILr4fxer8l28S8tk1d3ZT6PSWv7Wt1c5qEvO7AVVXrZLzW9+Cq6mS8VnfxOj3Ans3rPlynV7Dn9KrXv1tNrJL0Wv52PfqOF1544fUoXlu8rvfaruqqqpstXld5beLO+3WL1zVeU6NihdcVXrO95Bavy16r8/ubt6re4DXzWp71WlXVEbCUvd7O9RvC4PsExvp1fP0a5+pHsKSfj1Ov9e10m+wDWNJeYy9/da6reAiWeH3fvFbVcn2+CXsAxn77cs96DobXFS3+GRhe15yI7MHwuuoAaQLDa1PX68vnbSNY8l6bel7enz6erLd47Vtg68unue947TuGAezs4TdeM66qWl+6K4DXnKuq1heuVuD1s/nKlQm88MILL7zwSsRrvfxCNnhxPwcvvPDCCy+85u8rLH83dWJeNe93fMnrTu8PbVLx2tyFa5nO+4/NHbjqTULvIzd/kusZ33ffrG5a9F/PXtd/Qi/+nwJeeOGFF1544YUXXnjhhRdeeOGFF1544YUXXnjhhRdeeOGFF1544YUXXnjhhRdeeKXo1f36a/knvZ4yeOGFF1544UXwwgsvvPDC61viFJPvLn/ZSzleY8rbvcpr/sqzeMnjdY1XbmbWZNICr6u9zEsazHs/dLlKs95Lku/D15qF5JrjXt5JKneJeTWSepPUSCqtHSfp6CBJmZSbSWrNrJfUm/VuWv7it5TQ+AooKvuwnHlJXbDcBbVPXk4qzHaZ1KTkFdcvSSoHsyJOrFIqzFx8FuSfvRrJmZm9SEWCz0dFvixOxFbS0IdRNq73B16F9JJoPVGamaRmP9fih3acY81nLxd+IT2vUN8frk14nV7vQ6LF4Xwcjs7HTlJveZyPvfc+Ya/Zep+bLeJ6vxi9/FSA+Lje+3TW+2Ne3ad6wk/1xEJyvXVOUm9DrCfCL5bfvyF/DC/bHatXw/hqwg/BK7iNjwufTr36wevofqj3vjGz3UKSH8ZnQtgPtVPV+9Re/3zwwgsvvPDCi+CFF1544fWd8TleeN2YZjHrT/hMcuG+QPsyb1zgFZPHtlZr+zNZ148trrExhlfMy3Rs1Jstxs9u30eMzS+8zCwc/hRhML2M7ehC0q6QXOfz3n1/p/6RvcZjjEIqpsOPTPJO8uZz81KG15QyHpOVUmEuLlZO8ovoxfPxIDvv25HIvPd9nKOdl1TkDq9Tw8wN8fOwkIrpQZD5Aa8P6fJQQoQRN54kdbGiyFq8Pq75KuIoGor5/VbvvKSsx2tWUSwkN9akbSbl3Xw/NLgr7gcn5NVl+8E1FmAW7gkM5nOzHK9ZhmxWj7baX1rKYj3RZ3gd3Q7J7bdDUhnrCdavw2Qzr07zO4j59Jnn4z4zItceeFkTmhXlgzR06BfilbrXIwUvvPDCCy+8CF544YUXXngRvPDCC69nzv/F6x/Kmshd1QAAAABJRU5ErkJggg==',
						),
						'right'  => array(
							'name' => __( 'Right', 'neve' ),
							'url'  => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAS8AAADYCAAAAACfVToRAAAEs0lEQVR42u3cLXPrOhSF4fP/6aKGgoFmhmZlspvUaXIm5HguMTIz2hdIdpw2X23SaRu9C6UzacEzkry1JfePkY/kDwR44YUXXngRvPDCCy+88CJ44YUXXniRr/f67++3BS+88MILL7zwwgsvvPDCCy+88MILL7zwwgsvvPDCCy+88MILL7zwwgsvvPDCCy+8fozX6y3ZJua1berqptSvKXltn6ub0yTkdQeuqlon47W+B1dVJ+O1uovX6QH2aF734Tq9gj2mV73+bDWxStJr+el69BUvvPDC66d4bfG63mu7qquqbrZ4XeW1iTvv5y1e13hNjYoVXld4zfaSW7wue63O729eqnqD18xredZrVVVHwFL2ejnXbwiD7x0Y69fx9Wucq2/Bkn4+Tr3Wl9NtsjdgSXuNvfzVua7iIVji9X3zXFXL9fkm7AEY++3LPes5GF5XtPhnYHhdcyKyB8PrqgOkCQyvTV2vL5+3jWDJe23qeXl/+niy3uK1b4GtL5/mvuK17xgGsLOH33jNuKpqfemuAF5zrqpaX7hagdff5iNXJvDCCy+88MIrEa/18gPZ4MX9HLzwwgsvvPCav6+w/GzqxLxq3u/4kNed3h/apOK1uQvXMp33H5s7cNWbhN5Hbr6S6xHfd9+sblr0n89e139AL/6fAl544YUXXnjhhRdeeOGFF1544YUXXnjhhRdeeOGFF1544YUXXnjhhRdeeOGFF154pejV/fu2/EqvhwxeeOGFF154EbzwwgsvvPD6rjjF5LvLX/ZSjteY8nav8pq/8ihe8nhd45WbmTWZtMDrai/zkgbz3g9drtKs95Lk+/C1ZiG55riXd5LKXWJejaTeJDWSSmvHSTo6SFIm5WaSWjPrJfVmvZuWv/gtJTS+AorKPixnXlIXLHdB7Z2XkwqzXSY1KXnF9UuSysGsiBOrlAozF58F+XuvRnJmZk9SkeDzUZEvixOxlTT0YZSN6/2BVyE9JVpPlGYmqdnPtfihHedY897LhV9IzyvU94drE16n1/uQaHE4H4ej87GT1Fse52PvvU/Ya7be52aLuN4vRi8/FSA+rvc+nfX+mFf3rp7wUz2xkFxvnZPU2xDrifCL5RdvyH+sl+2O1athfDXhh+AV3MbHhU+nXn3jdXQ/1HvfmNluIckP4zMh7Ifaqep9aK/fFbzwwgsvvPAieOGFF154/dD4HC+8bkyzmPUtfCa5cI+gfZo3NPCKyWO7q7X9Wa3rx9bX2DDDK+ZpOk7qzRbjZ7fvL8amGF5mFg6FijCYnsY2dSFpV0iu83nvvrSD/+u8xuONQiqmQ5FM8k7y5nPzUobXlDIen5VSYS4uVk7yi+jF8/EgO+/bkci8932co52XVOQOr1PDzA3x87CQiulBkPkBrzfp8lBChBE3njB1saLIWrzervkq4igaivm9V++8pKzHa1ZRLCQ31qRtJuXdfD80uCvuDSfk1WX7wTUWYBbuDwzmc7Mcr1mGbFaPttpfZspiPdFneB3dDsntt0NSGesJ1q/DZDOvTvO7ifn0mefjPjMi1x54WROaFeVXNnToF+KF188JXnjhhRdeeBG88MILL7zwInjhhRdej5z/AdA+H8o+3vutAAAAAElFTkSuQmCC',
						),
						'inline' => array(
							'name' => __( 'Inline', 'neve' ),
							'url'  => 'data:image/png;base64,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',
						),
					),
				),
				version_compare( NEVE_VERSION, '2.10.1', '<' ) ? 'Neve\Customizer\Controls\Radio_Image' : '\Neve\Customizer\Controls\React\Radio_Image'
			)
		);

		$this->add_control(
			new Control(
				'neve_advanced_reviews',
				array(
					'sanitize_callback' => 'neve_sanitize_checkbox',
					'default'           => false,
				),
				array(
					'label'    => esc_html__( 'Advanced reviews', 'neve' ),
					'section'  => 'woocommerce_product_catalog',
					'type'     => 'neve_toggle_control',
					'priority' => 530,
				)
			)
		);
	}

	/**
	 * Add sale tag customizer controls.
	 */
	private function add_sale_tag_controls() {

		$this->add_control(
			new Control(
				'neve_sale_tag_position',
				array(
					'default'           => 'inside',
					'sanitize_callback' => array( $this, 'sanitize_sale_tag_position' ),
				),
				array(
					'label'         => esc_html__( 'Position', 'neve' ),
					'section'       => 'woocommerce_product_catalog',
					'priority'      => 610,
					'documentation' => [
						'label' => __( 'View more details about this', 'neve' ),
						'link'  => 'https://bit.ly/neve-woo-st',
					],
					'choices'       => array(
						'inside'  => array(
							'name' => __( 'Inside', 'neve' ),
							'url'  => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAS8AAADYCAMAAACN4JX/AAAAVFBMVEU/xPtTyPtkzPx00fyD1fyR2fye3f2q4f225f3B6f3M7P7S0tLT09PU1NTV1dXW1tbX19fX8P7Y2NjZ2dna2trb29vc3Nzd3d3i9P7s+P71+/////8xOQAQAAADLElEQVR4AezQMQ0AMAwDsLDYMf48i6Gq8tkQnL+BL1++fPnyhS9fvnz58oUvX758+cKXL1++fPnCly9fvnz5ov3lC1++fPnKwWvx5cuXL1++fPny5cuXL1++fPny5cuXL1++fPnyNezXsW7EIAyAYRPiM8eWjfh///dsiNOrVJW1k/8BmfUTIPEPXsUc3GJTwe/h5JTo5GlPr8n1eIwydw3Q9GLt1UDv1SaeA/2XV0xRevW4gM77Wl/QoKTX2stgWL2GqXTQC1h6rb3kYNb1GjdQ6Yw/368jvaLdHMBEDBdRqOm19IrMoXxoWt7HlVeBdoPBtvPk6bU8Xwe8ROrA5R0yCppeK6/qRPp91MTpP6/WZ5qsUf6HoFUx2GXWYftqx25zrIZhMAovIElTtfnY/05B/LhKecHcMEMYxefsoI/qSPb/8mLfxuv42/LVPHqFD5QbXlPFgtdcBa+pYsNrqozXXA2vqW68pjrxmiqzD73R+fI68HqjhhdeeOGFF1544YUXXu/Wf5l+UW8xvEp/NMEryeJnhNcdHlWbBK8klwUjvFp4Fk2QI8Tbt1cNP2VxpfC9C68xm0vBmEebS8FcefX49DosLgFz6HU+vW6DS8A8erU4QiSDS8BcevUyMMRqcAmYT69+xwkuAfPn1Vv+IRbPZnEJmDevsVpK7d3mEjDHXppyKRheE1wChpfJJWB42VwChpdyCZhbr5LSNcElYM68ilxXDS4Bc+AlXAOYxSVVH17CNYJNcIXiw0u4RjDhwku4RjDhwku4BjDhwku4BjCDC6+iAvn3XHjlGRu88MILL7zwwgsvvPBa7HXnD1a538+HF1544YUXXnjhZYdXbJ/vVTb2CvH49MKrvItXDEs6d/FKa7yuXbzONV51F6+2hOvoG3gt/MHqPl49rXi9NvJqaQHXBl6rRjKWvplXb2eK/wjruHvfwGthG3jhhRdeeOGFF1544YUXXnjhhRdeeOGFF1544YUXXnjhhRdeeOGFF1544YUXXnjhhRdeeOFFX9YLL7wIL7zwwgsvvAgvvPDCCy+8CC+88MILL8ILL7zwwgsvwgsvvPDCi74BKevIZRVAXf8AAAAASUVORK5CYII=',
						),
						'outside' => array(
							'name' => __( 'Outside', 'neve' ),
							'url'  => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAS8AAADYCAMAAACN4JX/AAAAVFBMVEU/xPtTyPtkzPx00fyD1fyR2fye3f2q4f225f3B6f3M7P7S0tLT09PU1NTV1dXW1tbX19fX8P7Y2NjZ2dna2trb29vc3Nzd3d3i9P7s+P71+/////8xOQAQAAADR0lEQVR4AezXMWvDMBBA4XNsRY62THX0/v//bA8lKQQ0lIZ2eW8w5/XDZ7jAfpJeeumll156mV566fWW9NJLL71ML7300kuv+EV6PdJLL7300ksvvfTSSy+99NJLL7300ksvvT7+ML30eld66aXXUjv0Ol5W6JHduMXoxr1Nr+S6exxLfLUDRa+5VwqVfFITrwPtxWtMI73aWMDOJSLOsMOi19yrwlHXiEilK22BqtfUK42yViLiBCUax4vX6KrXaKsdoEZUekSBVa+p1yjJlifNPttHvZbUSTA4bdzres284grniPWgx2XIFCh6zbzWzqg8PrXotO+/1nNK1pH3EOxrVNgia3DS67/vbb300ksvvfTSSy+99NJLL7300ksvvfTSSy+9Ptu511y3QSiKwgPggWXzmP9MW/UPanfvaaISdHVYawif4sTaKOSFlbu79wprKx2vt4p1v9eCxhdd4eNVR15P+Hyxu/HqMWyouPG6w5a6F6+8x+vx4hX3eF1evMKeij+vNpalryrZnVce6+t44YUXXnjhhde5XrWkGNPVXkHBq2frtXyGl0w8Ca9/eqWXliu8/j64Nrxsr2QsCxJePfxeNEFyiM/ZXi38kcWVws9uwwsv4TLAeB6FS8AO85JFP1tcAnaal553PwaXgJ3mpSe4yeASsCO9Rg2z2AwuATvTazzxFS4BO9Zr9PJLLF7d5lKwY/fCVmuzfxltMPZVm0vB8LK5BAwvm0vA8LK5BAwv5RKwY71qSrfBZYAd6VV1XbW5FMy3l3BNMJNLakd4CdcEs7mkeo6XcE0w5cJLuCaYcuElXBNMuPASrglmcOFVVaB8zYVXeRlmvxdeeOGFF1544YUXXng95T9rq734fwdeeOGFF1544YUXXrGv96qOvULMy3N4Hwz3Db3nlfZ43V68rj1ezYtX38KVhxevPR+w5sdrpJ3fXg68etrF5cBrwyMZ63DmNfqV4oew8jOGK6/94YUXXnjhhRdeeOGFF1544YUXXnjhhRdeeOGFF1544YUXXnjhhRdeeOGFF1544YUXXnjh5cCL8MILL7zwwovwwgsvvPDCi/DCCy+88CK88MILL7zwIrzwwgsvvPAivPD6Lv0ALgqZQ3JkuCMAAAAASUVORK5CYII=',
						),
					),
				),
				version_compare( NEVE_VERSION, '2.10.1', '<' ) ? 'Neve\Customizer\Controls\Radio_Image' : '\Neve\Customizer\Controls\React\Radio_Image'
			)
		);

		$this->add_control(
			new Control(
				'neve_sale_tag_alignment',
				array(
					'default'           => 'left',
					'sanitize_callback' => array( $this, 'sanitize_sale_tag_alignment' ),
				),
				array(
					'label'    => esc_html__( 'Alignment', 'neve' ),
					'section'  => 'woocommerce_product_catalog',
					'priority' => 620,
					'type'     => 'select',
					'choices'  => array(
						'left'  => esc_html__( 'Left', 'neve' ),
						'right' => esc_html__( 'Right', 'neve' ),
					),
				)
			)
		);

		$this->add_control(
			new Control(
				'neve_sale_tag_radius',
				array(
					'sanitize_callback' => 'absint',
					'default'           => 0,
				),
				array(
					'label'       => esc_html__( 'Border radius (%)', 'neve' ),
					'section'     => 'woocommerce_product_catalog',
					'type'        => 'neve_range_control',
					'step'        => 1,
					'input_attr'  => array(
						'min'     => 0,
						'max'     => 50,
						'default' => 0,
					),
					'input_attrs' => array(
						'min'        => 0,
						'max'        => 50,
						'defaultVal' => 0,
					),
					'priority'    => 630,
				),
				class_exists( 'Neve\Customizer\Controls\React\Range' ) ? 'Neve\Customizer\Controls\React\Range' : 'Neve\Customizer\Controls\Range'
			)
		);

		$this->add_control(
			new Control(
				'neve_sale_tag_text',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'label'    => esc_html__( 'Text', 'neve' ),
					'section'  => 'woocommerce_product_catalog',
					'priority' => 640,
				)
			)
		);

		$this->add_control(
			new Control(
				'neve_enable_sale_percentage',
				array(
					'sanitize_callback' => 'neve_sanitize_checkbox',
					'default'           => false,
				),
				array(
					'label'    => esc_html__( 'Enable sale percentage', 'neve' ),
					'section'  => 'woocommerce_product_catalog',
					'type'     => 'neve_toggle_control',
					'priority' => 650,
				)
			)
		);

		$this->add_control(
			new Control(
				'neve_sale_percentage_format',
				array(
					'sanitize_callback' => 'sanitize_text_field',
					'default'           => '{value}%',
				),
				array(
					'label'           => esc_html__( 'Sale tag format', 'neve' ),
					'description'     => esc_html__( 'How the discount should be displayed. e.g. {value}%', 'neve' ),
					'section'         => 'woocommerce_product_catalog',
					'type'            => 'text',
					'priority'        => 655,
					'active_callback' => array( $this, 'neve_sale_percentage_format_active_callback' ),
				)
			)
		);

		$sale_tag_default = version_compare( NEVE_VERSION, '2.9.0', '<' ) ? '#2dce89' : 'var(--nv-c-1)';

		$this->add_control(
			new Control(
				'neve_sale_tag_color',
				array(
					'sanitize_callback' => 'neve_sanitize_colors',
					'default'           => $sale_tag_default,
				),
				array(
					'label'    => esc_html__( 'Background Color', 'neve' ),
					'section'  => 'woocommerce_product_catalog',
					'type'     => 'neve_color_control',
					'priority' => 660,
				)
			)
		);

		$this->add_control(
			new Control(
				'neve_sale_tag_text_color',
				array(
					'sanitize_callback' => 'neve_sanitize_colors',
					'default'           => '#ffffff',
				),
				array(
					'label'    => esc_html__( 'Text Color', 'neve' ),
					'section'  => 'woocommerce_product_catalog',
					'type'     => 'neve_color_control',
					'priority' => 670,
				)
			)
		);
	}

	/**
	 * Sanitize the shop layout value.
	 *
	 * @param string $value Value from the control.
	 *
	 * @return string
	 */
	public function sanitize_shop_layout( $value ) {
		$allowed_values = array( 'list', 'grid' );
		if ( ! in_array( $value, $allowed_values, true ) ) {
			return 'grid';
		}

		return esc_html( $value );
	}

	/**
	 * Sanitize the pagination type
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_pagination_type( $value ) {
		$allowed_values = array( 'number', 'infinite' );
		if ( ! in_array( $value, $allowed_values, true ) ) {
			return 'number';
		}

		return esc_html( $value );
	}

	/**
	 * Sanitize add to cart position control.
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_add_to_cart_display( $value ) {
		$allowed_values = array( 'none', 'after', 'on_image' );
		if ( ! in_array( $value, $allowed_values, true ) ) {
			return 'number';
		}

		return esc_html( $value );
	}

	/**
	 * Sanitize content order control.
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_product_elements_ordering( $value ) {
		$allowed = array(
			'category',
			'title',
			'short-description',
			'reviews',
			'price',
		);

		if ( empty( $value ) ) {
			return wp_json_encode( $allowed );
		}

		$decoded = json_decode( $value, true );

		foreach ( $decoded as $val ) {
			if ( ! in_array( $val, $allowed, true ) ) {
				return wp_json_encode( $allowed );
			}
		}

		return $value;
	}

	/**
	 * Sanitize button position.
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_button_position( $value ) {
		$allowed_values = array( 'none', 'top', 'bottom' );
		if ( ! in_array( $value, $allowed_values, true ) ) {
			return 'none';
		}

		return esc_html( $value );
	}

	/**
	 * Sanitize sale bubble position.
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_sale_tag_position( $value ) {
		$allowed_values = array( 'inside', 'outside' );
		if ( ! in_array( $value, $allowed_values, true ) ) {
			return 'inside';
		}

		return esc_html( $value );
	}

	/**
	 * Sanitize sale tag alignment.
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_sale_tag_alignment( $value ) {
		$allowed_values = array( 'left', 'right' );
		if ( ! in_array( $value, $allowed_values, true ) ) {
			return 'left';
		}

		return esc_html( $value );
	}

	/**
	 * Sanitize product content alignment.
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_product_content_alignment( $value ) {
		$allowed_values = array( 'left', 'right', 'center', 'inline' );
		if ( ! in_array( $value, $allowed_values, true ) ) {
			return 'left';
		}

		return esc_html( $value );
	}

	/**
	 * Sanitize image hover control.
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_image_hover( $value ) {
		$allowed_values = array(
			'none',
			'swipe',
			'zoom',
			'blur',
			'fadein',
			'fadeout',
			'glow',
			'colorize',
			'grayscale',
		);
		if ( ! in_array( $value, $allowed_values, true ) ) {
			return 'none';
		}

		return esc_html( $value );
	}

	/**
	 * Sanitize category card layout.
	 *
	 * @param string $value Control value.
	 *
	 * @return string
	 */
	public function sanitize_category_card_layout( $value ) {
		$allowed_values = array(
			'default',
			'style-1',
			'style-2',
			'style-3',
			'style-4',
			'style-5',
			'style-6',
		);
		if ( ! in_array( $value, $allowed_values, true ) ) {
			return 'default';
		}

		return esc_html( $value );
	}

	/**
	 * Decide if image height control should be visible based on Force Same Image Height control.
	 *
	 * @return bool
	 */
	public function same_image_height_active_callback() {
		return get_theme_mod( 'neve_force_same_image_height' );
	}

	/**
	 * Decide if products per row should be visible based on Layout control.
	 *
	 * @return bool
	 */
	public function products_per_row_active_callback() {
		$layout_toggle = get_theme_mod( 'neve_enable_product_layout_toggle', false );
		if ( $layout_toggle === true ) {
			return true;
		}

		return get_theme_mod( 'neve_product_card_layout', 'grid' ) === 'grid';
	}

	/**
	 * Add off canvas option for shop layout.
	 *
	 * @param array  $current_settings Current control settings.
	 * @param string $control_id Current control id.
	 *
	 * @return array
	 */
	public function add_off_canvas_option( $current_settings, $control_id ) {
		if ( $control_id !== 'neve_shop_archive_sidebar_layout' ) {
			return $current_settings;
		}
		$current_settings['off-canvas'] = array(
			'url' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAABqCAMAAABpj1iyAAAACVBMVEX///8+yP/V1dXG9YqxAAAACXBIWXMAAAsTAAALEwEAmpwYAAAG0mlUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS42LWMxNDUgNzkuMTYzNDk5LCAyMDE4LzA4LzEzLTE2OjQwOjIyICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtbG5zOmRjPSJodHRwOi8vcHVybC5vcmcvZGMvZWxlbWVudHMvMS4xLyIgeG1sbnM6cGhvdG9zaG9wPSJodHRwOi8vbnMuYWRvYmUuY29tL3Bob3Rvc2hvcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RFdnQ9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZUV2ZW50IyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ0MgMjAxOSAoTWFjaW50b3NoKSIgeG1wOkNyZWF0ZURhdGU9IjIwMTktMTItMTlUMTA6NDQ6MTkrMDI6MDAiIHhtcDpNb2RpZnlEYXRlPSIyMDE5LTEyLTE5VDExOjE5OjI0KzAyOjAwIiB4bXA6TWV0YWRhdGFEYXRlPSIyMDE5LTEyLTE5VDExOjE5OjI0KzAyOjAwIiBkYzpmb3JtYXQ9ImltYWdlL3BuZyIgcGhvdG9zaG9wOkNvbG9yTW9kZT0iMiIgcGhvdG9zaG9wOklDQ1Byb2ZpbGU9InNSR0IgSUVDNjE5NjYtMi4xIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjliNzJmY2RjLWU4MjgtNDQyMC1iOTBmLTJmNWQ4ZGRmOTkxMiIgeG1wTU06RG9jdW1lbnRJRD0iYWRvYmU6ZG9jaWQ6cGhvdG9zaG9wOmJmMGE1MTJlLTg1NzctMGY0My1iMzY3LTQ1ZDU2NTZiN2M3ZSIgeG1wTU06T3JpZ2luYWxEb2N1bWVudElEPSJ4bXAuZGlkOjE1YWZmYmE0LWZjNjItNGU2Yi05ZGI3LTNmNzYxZGQ4MTE5NSI+IDx4bXBNTTpIaXN0b3J5PiA8cmRmOlNlcT4gPHJkZjpsaSBzdEV2dDphY3Rpb249ImNyZWF0ZWQiIHN0RXZ0Omluc3RhbmNlSUQ9InhtcC5paWQ6MTVhZmZiYTQtZmM2Mi00ZTZiLTlkYjctM2Y3NjFkZDgxMTk1IiBzdEV2dDp3aGVuPSIyMDE5LTEyLTE5VDEwOjQ0OjE5KzAyOjAwIiBzdEV2dDpzb2Z0d2FyZUFnZW50PSJBZG9iZSBQaG90b3Nob3AgQ0MgMjAxOSAoTWFjaW50b3NoKSIvPiA8cmRmOmxpIHN0RXZ0OmFjdGlvbj0ic2F2ZWQiIHN0RXZ0Omluc3RhbmNlSUQ9InhtcC5paWQ6Mjk3NDU4ZDktY2M0YS00M2M2LWIxZmEtMjRkMTNmMTVlNTM1IiBzdEV2dDp3aGVuPSIyMDE5LTEyLTE5VDEwOjU0OjUzKzAyOjAwIiBzdEV2dDpzb2Z0d2FyZUFnZW50PSJBZG9iZSBQaG90b3Nob3AgQ0MgMjAxOSAoTWFjaW50b3NoKSIgc3RFdnQ6Y2hhbmdlZD0iLyIvPiA8cmRmOmxpIHN0RXZ0OmFjdGlvbj0ic2F2ZWQiIHN0RXZ0Omluc3RhbmNlSUQ9InhtcC5paWQ6OWI3MmZjZGMtZTgyOC00NDIwLWI5MGYtMmY1ZDhkZGY5OTEyIiBzdEV2dDp3aGVuPSIyMDE5LTEyLTE5VDExOjE5OjI0KzAyOjAwIiBzdEV2dDpzb2Z0d2FyZUFnZW50PSJBZG9iZSBQaG90b3Nob3AgQ0MgMjAxOSAoTWFjaW50b3NoKSIgc3RFdnQ6Y2hhbmdlZD0iLyIvPiA8L3JkZjpTZXE+IDwveG1wTU06SGlzdG9yeT4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz5LRiqyAAAA6klEQVRoge3UQQ7CMBBD0WHE/Y88sCBUjRoCRil48d8GhUbUZNxGAAAAAJi6vL6U3apODtK7fr41I6IiK7LaKmr/sTK4EKu/b0v3/LKirdfI91v+YXJahz+fozm18eXPhnio/PC+2xCXnvu6H6uVuZTKb/bP3epn8MH0vQXFZIjDS5P93xmXw/R1qp7W7awgPdPTMo1F5RVUXmEai8orqLzCNBaVV1B5hWksKq+g8grTWFReQeUVprGovILKK0xjUXkFlVeYxqLyCiqvMI1F5RVUXmEai8orqLzCNBaVV1B5hWksKg8AAHCuO0cqMnC+e7cbAAAAAElFTkSuQmCC',
		);

		return $current_settings;
	}

	/**
	 * Active callback for sale percentage format control
	 *
	 * @return bool
	 */
	public function neve_sale_percentage_format_active_callback() {
		return get_theme_mod( 'neve_enable_sale_percentage', false ) === true;
	}

}
