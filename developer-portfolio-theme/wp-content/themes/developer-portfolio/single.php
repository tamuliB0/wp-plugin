<?php
/**
 * Template for single posts.
 *
 * @package Developer_Portfolio
 */

get_header(); ?>
<?php post_class(); ?>
<?php
if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
		?>
		<?php get_template_part( 'template-parts/content' ); ?>

		<?php
	endwhile;
else :
	esc_html_e( 'No posts found', 'developer-portfolio' );
endif;

get_footer();

