<?php
/**
 * Author:          Andrei Baicus <andrei@themeisle.com>
 * Created on:      28/08/2018
 *
 * @package Neve
 */

$container_class = apply_filters( 'neve_container_class_filter', 'container', 'single-post' );

// --------- START CUSTOMIZATION ------------------
/**
 * This is the same function as render_tags_list in post_meta.php,
 * but modified in order to display a custom taxonomy (get_the_tags => get_the_terms)
 * 
 * Render the terms list.
 */
function render_terms_list($postId) {
	
	// This is the only changed line:
	//$tags = get_the_tags();
	$tags = get_the_terms($postId, 'project_tags');

	if ( ! is_array( $tags ) ) {
		return;
	}
	$html  = '<div class="nv-tags-list">';
	$html .= '<span>' . __( 'Tags', 'neve' ) . ':</span>';
	foreach ( $tags as $tag ) {
		$tag_link = get_tag_link( $tag->term_id );
		$html    .= '<a href=' . esc_url( $tag_link ) . ' title="' . esc_attr( $tag->name ) . '" class=' . esc_attr( $tag->slug ) . ' rel="tag">';
		$html    .= esc_html( $tag->name ) . '</a>';
	}
	$html .= ' </div> ';
	echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

// ------------ END CUSTOMIZATION ------------------

get_header();

?>

	<div class="<?php echo esc_attr( $container_class ); ?> single-post-container">
		<div class="row">
			<?php do_action( 'neve_do_sidebar', 'single-post', 'left' ); ?>
			<article id="post-<?php echo esc_attr( get_the_ID() ); ?>"
					class="<?php echo esc_attr( join( ' ', get_post_class( 'nv-single-post-wrap col' ) ) ); ?>">
				<?php
				/**
				 *  Executes actions before the post content.
				 *
				 * @since 2.3.8
				 */
				do_action( 'neve_before_post_content' );
				

				// --------- START CUSTOMIZATION ------------------
				?>

				<div class="nvjm-project-meta">
					
					<?= the_excerpt();?>

					<?php
					// Tags
					render_terms_list($post->ID);
					?>

				</div>

				<?php
				// ------------ END CUSTOMIZATION ------------------

				if ( have_posts() ) {
					while ( have_posts() ) {
						the_post();
						get_template_part( 'template-parts/content', 'single' );
					}
				} else {
					get_template_part( 'template-parts/content', 'none' );
				}

				/**
				 *  Executes actions after the post content.
				 *
				 * @since 2.3.8
				 */
				do_action( 'neve_after_post_content' );
				?>
			</article>
			<?php do_action( 'neve_do_sidebar', 'single-post', 'right' ); ?>
		</div>
	</div>
<?php
get_footer();


