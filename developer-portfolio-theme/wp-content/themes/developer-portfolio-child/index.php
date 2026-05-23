<?php
/**
 * Main template file.
 *
 * @package Developer_Portfolio_Child
 */

get_header();
?>
<p style="background:green;color:yellow">
	This is the Child theme template
</p>
<?php if ( have_posts() ) : ?>
	<?php
	while ( have_posts() ) :
		the_post();
		?>
	<h2>
		<a href="<?php echo esc_url( get_the_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
	</h2>
		<?php echo esc_html( get_the_date() ); ?>
	<?php endwhile; ?>
<?php else : ?>
	<?php esc_html_e( 'No posts found', 'developer-portfolio-child' ); ?>
<?php endif; ?>
	
<?php get_footer(); ?>
