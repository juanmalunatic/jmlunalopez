<?php
/**
 * File that handle dynamic css for Woo pro integration.
 *
 * @package Neve_Pro\Modules\Woocommerce_Booster
 */

namespace Neve_Pro\Modules\Woocommerce_Booster;

use Neve\Core\Settings\Config;
use Neve\Core\Settings\Mods;
use Neve\Core\Styles\Dynamic_Selector;
use Neve_Pro\Core\Generic_Style;
use Neve_Pro\Modules\Woocommerce_Booster\Customizer\Checkout_Page;

/**
 * Class Dynamic_Style
 *
 * @package Neve_Pro\Modules\Woocommerce_Booster
 */
class Dynamic_Style extends Generic_Style {
	const SAME_IMAGE_HEIGHT      = 'neve_force_same_image_height';
	const IMAGE_HEIGHT           = 'neve_image_height';
	const SALE_TAG_COLOR         = 'neve_sale_tag_color';
	const SALE_TAG_TEXT_COLOR    = 'neve_sale_tag_text_color';
	const SALE_TAG_RADIUS        = 'neve_sale_tag_radius';
	const BOX_SHADOW_INTENTISITY = 'neve_box_shadow_intensity';
	const THUMBNAIL_WIDTH        = 'woocommerce_thumbnail_image_width';

	// Sticky add to cart options
	const STICKY_ADD_TO_CART_BACKGROUND_COLOR = 'neve_sticky_add_to_cart_background_color';
	const STICKY_ADD_TO_CART_COLOR            = 'neve_sticky_add_to_cart_color';

	// Typography options
	const MODS_TYPEFACE_ARCHIVE_PRODUCT_TITLE      = 'neve_shop_archive_typography_product_title';
	const MODS_TYPEFACE_ARCHIVE_PRODUCT_PRICE      = 'neve_shop_archive_typography_product_price';
	const MODS_TYPEFACE_SINGLE_PRODUCT_TITLE       = 'neve_single_product_typography_title';
	const MODS_TYPEFACE_SINGLE_PRODUCT_PRICE       = 'neve_single_product_typography_price';
	const MODS_TYPEFACE_SINGLE_PRODUCT_META        = 'neve_single_product_typography_meta';
	const MODS_TYPEFACE_SINGLE_PRODUCT_DESCRIPTION = 'neve_single_product_typography_short_description';
	const MODS_TYPEFACE_SINGLE_PRODUCT_TABS        = 'neve_single_product_typography_tab_titles';
	const MODS_TYPEFACE_SHOP_NOTICE                = 'neve_shop_typography_alert_notice';
	const MODS_TYPEFACE_SHOP_SALE_TAG              = 'neve_shop_typography_sale_tag';

	// Checkout options
	const MODS_CHECKOUT_PAGE_LAYOUT           = 'neve_checkout_page_layout';
	const MODS_CHECKOUT_BOX_WIDTH             = 'neve_checkout_box_width';
	const MODS_CHECKOUT_BOXED_LAYOUT          = 'neve_checkout_boxed_layout';
	const MODS_CHECKOUT_PAGE_BACKGROUND_COLOR = 'neve_checkout_page_background_color';
	const MODS_CHECKOUT_BOX_BACKGROUND_COLOR  = 'neve_checkout_box_background_color';
	const MODS_CHECKOUT_BOX_PADDING           = 'neve_checkout_box_padding';

	// Products per row
	const MODS_SHOP_GRID_PRODUCTS_PER_ROW = 'neve_products_per_row';
	const MODS_SHOP_SINGLE_RELATED_COLUMN = 'neve_single_product_related_columns';


	/**
	 * Register Subscribe Groups
	 *
	 * @return array
	 */
	public function register_subscribers() {
		return [
			[
				'subscribers'       => [ $this, 'single_product_catalog_subscribers' ],
				// TODO: in next versions: update the return value to catch if current post contains products widget
				'activate_callback' => '__return_true',
			],
			[
				'subscribers'       => [ $this, 'sticky_add_to_cart_subscribers' ],
				'activate_callback' => 'is_product',
			],
			[
				'subscribers'       => [ $this, 'checkout_page_subscribers' ],
				'activate_callback' => 'is_checkout',
			],
		];
	}

	/**
	 * Add dynamic style subscribers.
	 *
	 * @param array $subscribers Css subscribers.
	 *
	 * @return array
	 */
	public function add_subscribers( $subscribers = [] ) {
		$dynamic_styles = $this->register_subscribers();

		// filter subscribers according to the activate status and call functions.
		foreach ( $dynamic_styles as $dynamic_style ) {
			if ( ! isset( $dynamic_style['activate_callback'] ) || ! isset( $dynamic_style['subscribers'] ) || ! call_user_func( $dynamic_style['activate_callback'] ) ) {
				continue;
			}

			$subscribers = call_user_func( $dynamic_style['subscribers'], $subscribers );
		}

		return $subscribers;
	}

	/**
	 * Dynamic style for the checkout page.
	 *
	 * @param array $subscribers Current subscribers array.
	 *
	 * @return array
	 */
	private function checkout_page_subscribers( $subscribers ) {
		if ( ! neve_pro_is_new_skin() ) {
			return $this->legacy_checkout_page_subscribers( $subscribers );
		}

		$is_boxed = Mods::get( self::MODS_CHECKOUT_BOXED_LAYOUT, Checkout_Page::get_checkout_boxed_layout_default() );

		if ( ! $is_boxed ) {
			return $subscribers;
		}

		$is_standard_layout          = Mods::get( self::MODS_CHECKOUT_PAGE_LAYOUT, 'standard' ) === 'standard';
		$checkout_background_default = 'var(--nv-site-bg)';

		if ( ! $is_standard_layout ) {
			$subscribers[] = [
				Dynamic_Selector::KEY_SELECTOR => '.nv-checkout-boxed-style',
				Dynamic_Selector::KEY_RULES    => [
					'--maxwidth' => [
						Dynamic_Selector::META_KEY    => self::MODS_CHECKOUT_BOX_WIDTH,
						Dynamic_Selector::META_IS_RESPONSIVE => true,
						Dynamic_Selector::META_SUFFIX => '%',
					],
				],
			];
		}

		$box_padding_default    = Checkout_Page::get_box_padding_default_value();
		$box_background_default = 'var(--nv-light-bg)';

		$subscribers[] = [
			Dynamic_Selector::KEY_SELECTOR => '.nv-checkout-boxed-style',
			Dynamic_Selector::KEY_RULES    => [
				'--bgcolor'    => [
					Dynamic_Selector::META_KEY     => self::MODS_CHECKOUT_PAGE_BACKGROUND_COLOR,
					Dynamic_Selector::META_DEFAULT => $checkout_background_default,
				],
				'--boxbgcolor' => [
					Dynamic_Selector::META_KEY     => self::MODS_CHECKOUT_BOX_BACKGROUND_COLOR,
					Dynamic_Selector::META_DEFAULT => $box_background_default,
				],
				'--boxpadding' => [
					Dynamic_Selector::META_KEY           => self::MODS_CHECKOUT_BOX_PADDING,
					Dynamic_Selector::META_IS_RESPONSIVE => true,
					Dynamic_Selector::META_DEFAULT       => $box_padding_default,
					Dynamic_Selector::META_SUFFIX        => 'responsive_unit',
					'directional-prop'                   => Config::CSS_PROP_PADDING,
				],
			],
		];

		return $subscribers;
	}

	/**
	 * Dynamic style for single product and catalog page.
	 *
	 * @param array $subscribers That current subscribers.
	 *
	 * @return array
	 */
	public function single_product_catalog_subscribers( $subscribers ) {
		if ( ! neve_pro_is_new_skin() ) {
			return $this->legacy_single_product_catalog_subscribers( $subscribers );
		}

		$subscribers[] = [
			'selectors' => '.products.related .products',
			'rules'     => [
				'--shopcoltemplate' => [
					Dynamic_Selector::META_DEFAULT     => 4,
					Dynamic_Selector::META_DEVICE_ONLY => 'desktop',
					Dynamic_Selector::META_KEY         => self::MODS_SHOP_SINGLE_RELATED_COLUMN,
				],
			],
		];


		$rules = [
			'--shopcoltemplate' => [
				Dynamic_Selector::META_KEY           => self::MODS_SHOP_GRID_PRODUCTS_PER_ROW,
				Dynamic_Selector::META_IS_RESPONSIVE => true,
				Dynamic_Selector::META_DEFAULT       => '{"desktop":3,"tablet":2,"mobile":2}',
			],
		];

		$same_image_height = Mods::get( self::SAME_IMAGE_HEIGHT );
		if ( $same_image_height === true ) {
			$rules['--sameimageheight'] = [
				Dynamic_Selector::META_KEY    => self::IMAGE_HEIGHT,
				Dynamic_Selector::META_SUFFIX => 'px',
			];
		}

		$subscribers[] = [
			'selectors' => ':root',
			'rules'     => $rules,
		];

		$subscribers[] = [
			'selectors' => '.product_title.entry-title',
			'rules'     => [
				'--h1texttransform' => [
					Dynamic_Selector::META_KEY => self::MODS_TYPEFACE_SINGLE_PRODUCT_TITLE . '.textTransform',
				],
				'--h1fontweight'    => [
					Dynamic_Selector::META_KEY => self::MODS_TYPEFACE_SINGLE_PRODUCT_TITLE . '.fontWeight',
					'font'                     => 'mods_' . Config::MODS_FONT_HEADINGS,
				],
				'--h1fontsize'      => [
					Dynamic_Selector::META_KEY           => self::MODS_TYPEFACE_SINGLE_PRODUCT_TITLE . '.fontSize',
					Dynamic_Selector::META_IS_RESPONSIVE => true,
					Dynamic_Selector::META_SUFFIX        => 'px',
				],
				'--h1lineheight'    => [
					Dynamic_Selector::META_KEY           => self::MODS_TYPEFACE_SINGLE_PRODUCT_TITLE . '.lineHeight',
					Dynamic_Selector::META_IS_RESPONSIVE => true,
					Dynamic_Selector::META_SUFFIX        => '',
				],
				'--h1letterspacing' => [
					Dynamic_Selector::META_KEY           => self::MODS_TYPEFACE_SINGLE_PRODUCT_TITLE . '.letterSpacing',
					Dynamic_Selector::META_IS_RESPONSIVE => true,
					Dynamic_Selector::META_SUFFIX        => 'px',
				],
			],
		];

		$shop_typography = [
			self::MODS_TYPEFACE_ARCHIVE_PRODUCT_TITLE      => '.woocommerce-loop-product__title',
			self::MODS_TYPEFACE_ARCHIVE_PRODUCT_PRICE      => 'ul.products .price',
			self::MODS_TYPEFACE_SINGLE_PRODUCT_PRICE       => '.summary .price',
			self::MODS_TYPEFACE_SINGLE_PRODUCT_META        => '.product_meta, .woocommerce-product-rating',
			self::MODS_TYPEFACE_SINGLE_PRODUCT_DESCRIPTION => '.single-product .entry-summary .woocommerce-product-details__short-description',
			self::MODS_TYPEFACE_SINGLE_PRODUCT_TABS        => '.woocommerce-tabs a',
			self::MODS_TYPEFACE_SHOP_NOTICE                => '.woocommerce-message,.woocommerce-error, .woocommerce-info',
			self::MODS_TYPEFACE_SHOP_SALE_TAG              => 'span.onsale',
		];

		foreach ( $shop_typography as $mod => $selector ) {
			$font          = $mod == self::MODS_TYPEFACE_ARCHIVE_PRODUCT_TITLE || $mod == self::MODS_TYPEFACE_SINGLE_PRODUCT_TITLE ? 'mods_' . Config::MODS_FONT_HEADINGS : 'mods_' . Config::MODS_FONT_GENERAL;
			$subscribers[] = [
				'selectors' => $selector,
				'rules'     => [
					'--texttransform' => [
						Dynamic_Selector::META_KEY => $mod . '.textTransform',
					],
					'--fontweight'    => [
						Dynamic_Selector::META_KEY => $mod . '.fontWeight',
						'font'                     => $font,
					],
					'--fontsize'      => [
						Dynamic_Selector::META_KEY    => $mod . '.fontSize',
						Dynamic_Selector::META_IS_RESPONSIVE => true,
						Dynamic_Selector::META_SUFFIX => 'px',
					],
					'--lineheight'    => [
						Dynamic_Selector::META_KEY    => $mod . '.lineHeight',
						Dynamic_Selector::META_IS_RESPONSIVE => true,
						Dynamic_Selector::META_SUFFIX => '',
					],
					'--letterspacing' => [
						Dynamic_Selector::META_KEY    => $mod . '.letterSpacing',
						Dynamic_Selector::META_IS_RESPONSIVE => true,
						Dynamic_Selector::META_SUFFIX => 'px',
					],
				],
			];
		}

		$subscribers[] = [
			'selectors' => '.woocommerce span.onsale',
			'rules'     => [
				Config::CSS_PROP_BACKGROUND_COLOR => self::SALE_TAG_COLOR,
				Config::CSS_PROP_COLOR            => self::SALE_TAG_TEXT_COLOR,
				Config::CSS_PROP_BORDER_RADIUS    => [
					Dynamic_Selector::META_KEY    => self::SALE_TAG_RADIUS,
					Dynamic_Selector::META_SUFFIX => '%',
				],
			],
		];

		return $subscribers;
	}

	/**
	 * Dynamic style for sticky add to cart
	 *
	 * @param array $subscribers That current subscribers.
	 *
	 * @return array
	 */
	public function sticky_add_to_cart_subscribers( $subscribers ) {
		if ( ! neve_pro_is_new_skin() ) {
			$subscribers['.sticky-add-to-cart--active'] = [
				'background-color' => [
					'key'     => self::STICKY_ADD_TO_CART_BACKGROUND_COLOR,
					'default' => version_compare( NEVE_VERSION, '2.9.0', '<' ) ? '#ffffff' : 'var(--nv-site-bg)',
				],
				'color'            => self::STICKY_ADD_TO_CART_COLOR,
			];

			return $subscribers;
		}


		$subscribers[] = [
			Dynamic_Selector::KEY_SELECTOR => '.sticky-add-to-cart--active',
			Dynamic_Selector::KEY_RULES    => [
				'--bgcolor' => self::STICKY_ADD_TO_CART_BACKGROUND_COLOR,
				'--color'   => self::STICKY_ADD_TO_CART_COLOR,
			],
		];

		return $subscribers;
	}

	/**
	 * Legacy dynamic style for the checkout page.
	 *
	 * @param array $subscribers Current subscribers array.
	 *
	 * @return array
	 */
	private function legacy_checkout_page_subscribers( $subscribers ) {
		$is_boxed                    = Mods::get( self::MODS_CHECKOUT_BOXED_LAYOUT, Checkout_Page::get_checkout_boxed_layout_default() );
		$checkout_background_default = version_compare( NEVE_VERSION, '2.9.0', '<' ) ? '#f7f7f7' : 'var(--nv-site-bg)';

		$subscribers['.woocommerce-checkout .woocommerce-checkout-review-order .woocommerce-checkout-review-order-table thead'] = [
			Config::CSS_PROP_BACKGROUND_COLOR => [
				Dynamic_Selector::META_KEY     => self::MODS_CHECKOUT_PAGE_BACKGROUND_COLOR,
				Dynamic_Selector::META_FILTER  => function ( $css_prop, $value ) use ( $is_boxed ) {
					$value = $is_boxed ? $value : 'inherit';
					if ( ! empty( $value ) ) {
						return sprintf( '%s:%s; filter: saturate(2);', $css_prop, $value );
					}

					return '';
				},
				Dynamic_Selector::META_DEFAULT => $checkout_background_default,
			],
		];

		if ( ! $is_boxed ) {
			return $subscribers;
		}

		$is_standard_layout = Mods::get( self::MODS_CHECKOUT_PAGE_LAYOUT, 'standard' ) === 'standard';
		if ( ! $is_standard_layout ) {
			$subscribers['.nv-checkout-boxed-style.nv-checkout-layout-stepped .woocommerce-checkout>.col2-set, .nv-checkout-boxed-style.nv-checkout-layout-vertical .woocommerce-checkout>.col2-set, .nv-checkout-boxed-style.nv-checkout-layout-stepped .woocommerce-checkout .woocommerce-checkout-review-order, .nv-checkout-boxed-style.nv-checkout-layout-vertical .woocommerce-checkout .woocommerce-checkout-review-order, .nv-checkout-boxed-style.nv-checkout-layout-stepped .next-step-button-wrapper'] = [
				Config::CSS_PROP_WIDTH => [
					Dynamic_Selector::META_KEY           => self::MODS_CHECKOUT_BOX_WIDTH,
					Dynamic_Selector::META_IS_RESPONSIVE => true,
					Dynamic_Selector::META_SUFFIX        => '%',
				],
			];
		}

		$subscribers['.nv-checkout-boxed-style, body.custom-background.nv-checkout-boxed-style'] = [
			Config::CSS_PROP_BACKGROUND_COLOR => [
				Dynamic_Selector::META_KEY     => self::MODS_CHECKOUT_PAGE_BACKGROUND_COLOR,
				Dynamic_Selector::META_DEFAULT => $checkout_background_default,
			],
		];

		$box_padding_default    = Checkout_Page::get_box_padding_default_value();
		$box_background_default = version_compare( NEVE_VERSION, '2.9.0', '<' ) ? '#ffffff' : 'var(--nv-light-bg)';

		$subscribers['.nv-checkout-boxed-style .col2-set, .nv-checkout-boxed-style .woocommerce-checkout-review-order-table, .nv-checkout-boxed-style.woocommerce-checkout #payment, .woocommerce-order-received.nv-checkout-boxed-style .woocommerce-order'] = [
			Config::CSS_PROP_BACKGROUND_COLOR => [
				Dynamic_Selector::META_KEY     => self::MODS_CHECKOUT_BOX_BACKGROUND_COLOR,
				Dynamic_Selector::META_DEFAULT => $box_background_default,
			],
			Config::CSS_PROP_PADDING          => [
				Dynamic_Selector::META_KEY           => self::MODS_CHECKOUT_BOX_PADDING,
				Dynamic_Selector::META_IS_RESPONSIVE => true,
				Dynamic_Selector::META_DEFAULT       => $box_padding_default,
			],
		];

		return $subscribers;
	}

	/**
	 * Legacy dynamic style for single product and catalog page.
	 *
	 * @param array $subscribers That current subscribers.
	 *
	 * @return array
	 */
	public function legacy_single_product_catalog_subscribers( $subscribers ) {
		/**
		 * Typography options
		 */
		$shop_typography = array(
			self::MODS_TYPEFACE_ARCHIVE_PRODUCT_TITLE      => '.woocommerce ul.products li.product .woocommerce-loop-product__title',
			self::MODS_TYPEFACE_ARCHIVE_PRODUCT_PRICE      => '.woocommerce ul.products li.product .price, .woocommerce ul.products li.product .price del, .woocommerce ul.products li.product .price ins',
			self::MODS_TYPEFACE_SINGLE_PRODUCT_TITLE       => '.woocommerce.single .product_title',
			self::MODS_TYPEFACE_SINGLE_PRODUCT_PRICE       => '.woocommerce div.product p.price, .woocommerce div.product p.price del, .woocommerce div.product p.price ins',
			self::MODS_TYPEFACE_SINGLE_PRODUCT_META        => '.product_meta, .woocommerce-product-rating',
			self::MODS_TYPEFACE_SINGLE_PRODUCT_DESCRIPTION => '.single-product .entry-summary .woocommerce-product-details__short-description',
			self::MODS_TYPEFACE_SINGLE_PRODUCT_TABS        => '.woocommerce div.product .woocommerce-tabs ul.tabs li a',
			self::MODS_TYPEFACE_SHOP_NOTICE                => '.woocommerce .woocommerce-message, .woocommerce-page .woocommerce-message, .woocommerce .woocommerce-error, .woocommerce-page .woocommerce-error',
			self::MODS_TYPEFACE_SHOP_SALE_TAG              => '.woocommerce span.onsale',
		);
		foreach ( $shop_typography as $mod => $selector ) {
			$font                     = $mod === self::MODS_TYPEFACE_ARCHIVE_PRODUCT_TITLE || $mod === self::MODS_TYPEFACE_SINGLE_PRODUCT_TITLE ? 'mods_' . Config::MODS_FONT_HEADINGS : 'mods_' . Config::MODS_FONT_GENERAL;
			$subscribers[ $selector ] = [
				'font-size'      => [
					'key'           => $mod . '.fontSize',
					'is_responsive' => true,
					'suffix'        => 'px',
				],
				'line-height'    => [
					'key'           => $mod . '.lineHeight',
					'is_responsive' => true,
					'suffix'        => '',
				],
				'letter-spacing' => [
					'key'           => $mod . '.letterSpacing',
					'is_responsive' => true,
				],
				'font-weight'    => [
					'key'  => $mod . '.fontWeight',
					'font' => $font,
				],
				'text-transform' => $mod . '.textTransform',
			];
		}

		$same_image_height = Mods::get( self::SAME_IMAGE_HEIGHT );
		if ( $same_image_height === true ) {
			$subscribers['.woocommerce ul.products li.product .sp-product-image.sp-same-image-height'] = [
				'height' => [
					'key'     => self::IMAGE_HEIGHT,
					'default' => 230,
				],
			];

			$subscribers['.woocommerce .nv-list ul.products.columns-neve li.product .sp-product-image.sp-same-image-height'] = [
				[
					'key'    => self::IMAGE_HEIGHT,
					'filter' => function ( $css_prop, $value, $meta, $device ) {
						$image_width = get_option( 'woocommerce_thumbnail_image_width' );

						return 'flex-basis: ' . $image_width . 'px;';
					},
				],
			];
		}
		if ( array_key_exists( '.woocommerce span.onsale', $subscribers ) ) {
			$subscribers['.woocommerce span.onsale'] = array_merge(
				$subscribers['.woocommerce span.onsale'],
				[
					'background-color' => self::SALE_TAG_COLOR,
					'color'            => self::SALE_TAG_TEXT_COLOR,
					'border-radius'    => [
						'key'    => self::SALE_TAG_RADIUS,
						'suffix' => '%',
					],
				]
			);
		} else {
			$subscribers['.woocommerce span.onsale'] = [
				'background-color' => self::SALE_TAG_COLOR,
				'color'            => self::SALE_TAG_TEXT_COLOR,
				'border-radius'    => [
					'key'    => self::SALE_TAG_RADIUS,
					'suffix' => '%',
				],
			];
		}

		$subscribers['.nv-product-content'] = [
			'padding' => [
				'key'    => self::BOX_SHADOW_INTENTISITY,
				'filter' => function ( $css_prop, $value, $meta, $device ) {
					if ( $value === 0 ) {
						return false;
					}

					return 'padding: 16px;';
				},
			],
		];

		$box_shadow = Mods::get( self::BOX_SHADOW_INTENTISITY, 0 );
		if ( $box_shadow !== 0 ) {
			$subscribers['.woocommerce ul.products li .nv-card-content-wrapper'] = [
				'box-shadow' => [
					'key'    => self::BOX_SHADOW_INTENTISITY,
					'filter' => function ( $css_prop, $value, $meta, $device ) {
						return 'box-shadow: 0px 1px 20px ' . ( $value - 20 ) . 'px rgba(0, 0, 0, 0.12);';
					},
				],
			];
		}

		return $subscribers;
	}
}
