<?php
/**
 * Template for static page.
 *
 * @package Developer_Portfolio
 */

get_header();

if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
		?>

	<h1><?php echo esc_html( get_the_title() ); ?></h1>
		<?php the_content(); ?>
	
	<?php endwhile;
else :
	esc_html_e( 'No page found', 'developer-portfolio' );
endif;
get_footer();
?>