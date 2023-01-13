<?php
/**
 * File that handle dynamic css for Lifter integration.
 *
 * @package Neve_Pro\Modules\LifterLMS_Booster
 */

namespace Neve_Pro\Modules\LifterLMS_Booster;

use Neve_Pro\Core\Generic_Style;
use Neve_Pro\Modules\LifterLMS_Booster\Views\Course_Membership;
use Neve\Core\Styles\Dynamic_Selector;

/**
 * Class Dynamic_Style
 *
 * @package Neve_Pro\Modules\LifterLMS_Booster
 */
class Dynamic_Style extends Generic_Style {
	const PRIMARY_COLOR         = 'neve_lifter_primary_color';
	const MEMBERSHIP_BOX_SHADOW = 'neve_membership_box_shadow_intensity';
	const COURSE_BOX_SHADOW     = 'neve_course_box_shadow_intensity';
	const COURSE_COLUMNS        = 'neve_courses_per_row';
	const MEMBERSHIP_COLUMNS    = 'neve_memberships_per_row';
	/**
	 * Main color elements selectors.
	 *
	 * @var array
	 */
	private $main_color_selectors = array(
		'border-color' =>
			'.llms-instructor-info .llms-instructors .llms-author,
			.llms-instructor-info .llms-instructors .llms-author .avatar,
			.llms-notification,
			.llms-checkout-section',
		'color'        =>
			'.llms-lesson-preview.is-complete .llms-lesson-complete,
			.llms-loop-item-content .llms-loop-title:hover',
		'background'   =>
			'.llms-instructor-info .llms-instructors .llms-author .avatar,
			.llms-access-plan-title,
			.llms-checkout-wrapper .llms-form-heading',

	);

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
			'--llmsprimarycolor'    => [
				Dynamic_Selector::META_KEY     => self::PRIMARY_COLOR,
				Dynamic_Selector::META_DEFAULT => 'var(--nv-primary-accent)',
			],
			'--llmsmbspboxshadow'   => [
				Dynamic_Selector::META_KEY     => self::MEMBERSHIP_BOX_SHADOW,
				Dynamic_Selector::META_DEFAULT => 0,
				Dynamic_Selector::META_FILTER  => function ( $css_prop, $value, $meta, $device ) {
					if ( $value === 0 ) {
						return '';
					}
					return sprintf( '%s:0px 1px 20px %s rgba(0, 0, 0, 0.12);', $css_prop, ( $value - 20 ) . 'px' );
				},
			],
			'--llmscourseboxshadow' => [
				Dynamic_Selector::META_KEY     => self::COURSE_BOX_SHADOW,
				Dynamic_Selector::META_DEFAULT => 0,
				Dynamic_Selector::META_FILTER  => function ( $css_prop, $value, $meta, $device ) {
					if ( $value === 0 ) {
						return '';
					}
					return sprintf( '%s:0px 1px 20px %s rgba(0, 0, 0, 0.12);', $css_prop, ( $value - 20 ) . 'px' );
				},
			],
			'--llmscoursecolumns'   => [
				Dynamic_Selector::META_KEY           => self::COURSE_COLUMNS,
				Dynamic_Selector::META_IS_RESPONSIVE => true,
				Dynamic_Selector::META_DEFAULT       => '{"desktop":3,"tablet":2,"mobile":1}',
			],
			'--llmsmbspcolumns'     => [
				Dynamic_Selector::META_KEY           => self::MEMBERSHIP_COLUMNS,
				Dynamic_Selector::META_IS_RESPONSIVE => true,
				Dynamic_Selector::META_DEFAULT       => '{"desktop":3,"tablet":2,"mobile":1}',
			],
		];

		$subscribers[] = [
			'selectors' => ':root',
			'rules'     => $rules,
		];

		return $subscribers;
	}

	/**
	 * Add legacy subscribers
	 *
	 * @param array $subscribers subscriber array.
	 *
	 * @return array
	 */
	private function add_legacy_subscribers( array $subscribers ) {

		$subscribers[ $this->main_color_selectors['border-color'] ] = [
			'border-color' => self::PRIMARY_COLOR,
		];
		$subscribers[ $this->main_color_selectors['color'] ]        = [
			'color' => self::PRIMARY_COLOR,
		];
		$subscribers[ $this->main_color_selectors['background'] ]   = [
			'background-color' => self::PRIMARY_COLOR,
		];

		$theme_mod = '';
		$context   = '';
		if ( Course_Membership::is_memberships() ) {
			$theme_mod = 'neve_membership_box_shadow_intensity';
			$context   = 'membership';
		}

		if ( Course_Membership::is_courses() ) {
			$theme_mod = 'neve_course_box_shadow_intensity';
			$context   = 'course';
		}
		if ( empty( $theme_mod ) || empty( $context ) ) {
			return $subscribers;
		}

		$subscribers[ '.llms-' . $context . '-list .llms-loop-item .llms-loop-item-content' ] = [
			'box-shadow' => [
				'key'    => $theme_mod,
				'filter' => function ( $css_prop, $value, $meta, $device ) {
					return 'box-shadow: 0px 1px 20px ' . ( $value - 20 ) . 'px rgba(0, 0, 0, 0.12);';
				},
			],
		];

		return $subscribers;
	}
}
