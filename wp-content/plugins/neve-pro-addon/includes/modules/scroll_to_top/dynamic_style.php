<?php
/**
 * File that handle dynamic css for Scroll to top integration.
 *
 * @package Neve_Pro\Modules\Scoll_To_Top
 */

namespace Neve_Pro\Modules\Scroll_To_Top;

use Neve\Core\Settings\Mods;
use Neve_Pro\Core\Generic_Style;
use Neve\Core\Styles\Dynamic_Selector;
use Neve\Core\Styles\Css_Prop;
use Neve\Core\Settings\Config;

/**
 * Class Dynamic_Style
 *
 * @package Neve_Pro\Modules\Scoll_To_Top
 */
class Dynamic_Style extends Generic_Style {
	const ICON_TOP_COLOR              = 'neve_scroll_to_top_icon_color';
	const ICON_BACKGROUND_COLOR       = 'neve_scroll_to_top_background_color';
	const ICON_TOP_COLOR_HOVER        = 'neve_scroll_to_top_icon_hover_color';
	const ICON_BACKGROUND_COLOR_HOVER = 'neve_scroll_to_top_background_hover_color';
	const ICON_IMAGE                  = 'neve_scroll_to_top_image';
	const ICON_TYPE                   = 'neve_scroll_to_top_type';

	const ICON_PADDING       = 'neve_scroll_to_top_padding';
	const ICON_BORDER_RADIUS = 'neve_scroll_to_top_border_radius';
	const ICON_SIZE          = 'neve_scroll_to_top_icon_size';
		
	/**
	 * Add dynamic style subscribers.
	 *
	 * @param array $subscribers Css subscribers.
	 *
	 * @return array|mixed
	 */
	public function add_subscribers( $subscribers = [] ) {
		if ( ! neve_pro_is_new_skin() ) {
			return $this->add_legacy_subscribers( $subscribers );
		}

		$rules = [
			'--color'        => [
				Dynamic_Selector::META_KEY     => self::ICON_TOP_COLOR,
				Dynamic_Selector::META_DEFAULT => empty( Mods::get( self::ICON_TOP_COLOR, 'var(--nv-text-dark-bg)' ) ) ? 'transparent' : 'var(--nv-text-dark-bg)',
			],
			'--padding'      => [
				Dynamic_Selector::META_KEY           => self::ICON_PADDING,
				Dynamic_Selector::META_IS_RESPONSIVE => true,
				Dynamic_Selector::META_SUFFIX        => 'responsive_unit',
				'directional-prop'                   => Config::CSS_PROP_PADDING,
				Dynamic_Selector::META_DEFAULT       => array(
					'desktop'      => array(
						'top'    => 8,
						'right'  => 10,
						'bottom' => 8,
						'left'   => 10,
					),
					'tablet'       => array(
						'top'    => 8,
						'right'  => 10,
						'bottom' => 8,
						'left'   => 10,
					),
					'mobile'       => array(
						'top'    => 8,
						'right'  => 10,
						'bottom' => 8,
						'left'   => 10,
					),
					'desktop-unit' => 'px',
					'tablet-unit'  => 'px',
					'mobile-unit'  => 'px',
				),
			],
			'--borderradius' => [
				Dynamic_Selector::META_KEY     => self::ICON_BORDER_RADIUS,
				Dynamic_Selector::META_DEFAULT => 3,
				Dynamic_Selector::META_SUFFIX  => 'px',
			],
			'--bgcolor'      => [
				Dynamic_Selector::META_KEY     => self::ICON_BACKGROUND_COLOR,
				Dynamic_Selector::META_DEFAULT => empty( Mods::get( self::ICON_BACKGROUND_COLOR, 'var(--nv-primary-accent)' ) ) ? 'transparent' : 'var(--nv-primary-accent)',
			],
			'--hovercolor'   => [
				Dynamic_Selector::META_KEY     => self::ICON_TOP_COLOR_HOVER,
				Dynamic_Selector::META_DEFAULT => empty( Mods::get( self::ICON_TOP_COLOR_HOVER, 'var(--nv-text-dark-bg)' ) ) ? 'transparent' : 'var(--nv-text-dark-bg)',
			],
			'--hoverbgcolor' => [
				Dynamic_Selector::META_KEY     => self::ICON_BACKGROUND_COLOR_HOVER,
				Dynamic_Selector::META_DEFAULT => empty( Mods::get( self::ICON_BACKGROUND_COLOR_HOVER, 'var(--nv-primary-accent)' ) ) ? 'transparent' : 'var(--nv-primary-accent)',
			],
			'--size'         => [
				Dynamic_Selector::META_KEY           => self::ICON_SIZE,
				Dynamic_Selector::META_DEFAULT       => '{ "mobile": "16", "tablet": "16", "desktop": "16" }',
				Dynamic_Selector::META_IS_RESPONSIVE => true,
				Dynamic_Selector::META_FILTER        => function ( $css_prop, $value, $meta, $device ) {
					$value = (int) $value;
					if ( $value > 0 ) {
						$unit_suffix = Css_Prop::get_suffix_responsive( $meta, $device );

						return sprintf( '%s:%s;', $css_prop, $value . $unit_suffix );
					}
					return '';
				},
			],
		];

		$type = Mods::get( self::ICON_TYPE, 'icon' );

		if ( $type === 'image' ) {
			$rules['--bgimage'] = [
				Dynamic_Selector::META_KEY    => self::ICON_IMAGE,
				Dynamic_Selector::META_FILTER => function ( $css_prop, $value, $meta, $device ) {
					return sprintf( '--bgimage:url(%s);', wp_get_attachment_url( $value ) );
				},
			];
		}

		$subscribers[] = [
			'selectors' => '.scroll-to-top',
			'rules'     => $rules,
		];

		return $subscribers;
	}

	/**
	 * Add legacy dynamic style subscribers.
	 *
	 * @param array $subscribers Css subscribers.
	 *
	 * @return array|mixed
	 */
	public function add_legacy_subscribers( $subscribers = [] ) {

		$subscribers[] = [
			'selectors' => '.scroll-to-top',
			'rules'     => [
				'color'         => [
					'key'     => self::ICON_TOP_COLOR,
					'default' => empty( Mods::get( self::ICON_TOP_COLOR, 'var(--nv-text-dark-bg)' ) ) ? 'transparent' : 'var(--nv-text-dark-bg)',
				],
				'padding'       => [
					'key'           => self::ICON_PADDING,
					'is_responsive' => true,
					'default'       => array(
						'desktop'      => array(
							'top'    => 8,
							'right'  => 10,
							'bottom' => 8,
							'left'   => 10,
						),
						'tablet'       => array(
							'top'    => 8,
							'right'  => 10,
							'bottom' => 8,
							'left'   => 10,
						),
						'mobile'       => array(
							'top'    => 8,
							'right'  => 10,
							'bottom' => 8,
							'left'   => 10,
						),
						'desktop-unit' => 'px',
						'tablet-unit'  => 'px',
						'mobile-unit'  => 'px',
					),
				],
				'border-radius' => [
					'key'     => self::ICON_BORDER_RADIUS,
					'default' => 3,
				],
				'background'    => [
					'key'     => self::ICON_BACKGROUND_COLOR,
					'default' => empty( Mods::get( self::ICON_BACKGROUND_COLOR, 'var(--nv-primary-accent)' ) ) ? 'transparent' : 'var(--nv-primary-accent)',
				],
			],
		];

		$subscribers[] = [
			'selectors' => '.scroll-to-top:hover, .scroll-to-top:focus',
			'rules'     => [
				'color'      => [
					'key'     => self::ICON_TOP_COLOR_HOVER,
					'default' => empty( Mods::get( self::ICON_TOP_COLOR_HOVER, 'var(--nv-text-dark-bg)' ) ) ? 'transparent' : 'var(--nv-text-dark-bg)',
				],
				'background' => [
					'key'     => self::ICON_BACKGROUND_COLOR_HOVER,
					'default' => empty( Mods::get( self::ICON_BACKGROUND_COLOR_HOVER, 'var(--nv-primary-accent)' ) ) ? 'transparent' : 'var(--nv-primary-accent)',
				],
			],
		];

		$type = Mods::get( self::ICON_TYPE, 'icon' );

		if ( $type === 'icon' ) {

			$subscribers[] = [
				'selectors' => '.scroll-to-top.icon .scroll-to-top-icon',
				'rules'     => [
					'width'  => [
						'key'           => self::ICON_SIZE,
						'default'       => '{ "mobile": "16", "tablet": "16", "desktop": "16" }',
						'is_responsive' => true,
						'filter'        => function ( $css_prop, $value, $meta, $device ) {
							$value = (int) $value;
							if ( $value > 0 ) {
								return sprintf( '%s:%s;', $css_prop, $value . 'px' );
							}
							return '';
						},
					],
					'height' => [
						'key'           => self::ICON_SIZE,
						'default'       => '{ "mobile": "16", "tablet": "16", "desktop": "16" }',
						'is_responsive' => true,
						'filter'        => function ( $css_prop, $value, $meta, $device ) {
							$value = (int) $value;
							if ( $value > 0 ) {
								return sprintf( '%s:%s;', $css_prop, $value . 'px' );
							}
							return '';
						},
					],
				],
			];
		}

		if ( $type === 'image' ) {
			$subscribers[] = [
				'selectors' => '.scroll-to-top.image .scroll-to-top-image',
				'rules'     => [
					'background-color' => [
						'key'    => self::ICON_IMAGE,
						'filter' => function ( $css_prop, $value, $meta, $device ) {
							$image  = wp_get_attachment_url( $value );
							$style  = '';
							$style .= sprintf( 'background-image: url("%s");', esc_url( $image ) );
							$style .= 'background-size:cover;';
							return $style;
						},
					],
				],
			];

			$subscribers['.scroll-to-top.image .scroll-to-top-image'] = [
				'width'  => [
					'key'           => self::ICON_SIZE,
					'default'       => '{ "mobile": "16", "tablet": "16", "desktop": "16" }',
					'is_responsive' => true,
					'filter'        => function ( $css_prop, $value, $meta, $device ) {
						$value = (int) $value;
						if ( $value > 0 ) {
							return sprintf( '%s:%s;', $css_prop, $value . 'px' );
						}
						return '';
					},
				],
				'height' => [
					'key'           => self::ICON_SIZE,
					'default'       => '{ "mobile": "16", "tablet": "16", "desktop": "16" }',
					'is_responsive' => true,
					'filter'        => function ( $css_prop, $value, $meta, $device ) {
						$value = (int) $value;
						if ( $value > 0 ) {
							return sprintf( '%s:%s;', $css_prop, $value . 'px' );
						}
						return '';
					},
				],
			];
		}

		return $subscribers;
	}
}
