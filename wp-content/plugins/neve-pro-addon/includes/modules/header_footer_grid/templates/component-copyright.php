<?php
/**
 * Template used for component rendering wrapper.
 *
 * Name:    Header Footer Grid
 *
 * @version 1.0.0
 * @package HFG
 */

namespace Neve_Pro\Modules\Header_Footer_Grid\Templates;

use Neve_Pro\Modules\Header_Footer_Grid\Components\Copyright;

$content = \HFG\component_setting( Copyright::CONTENT_ID );
?>
<div class="component-wrap">
	<div>
		<?php echo wp_kses_post( balanceTags( \HFG\parse_dynamic_tags( $content ), true ) ); ?>
	</div>
</div>

