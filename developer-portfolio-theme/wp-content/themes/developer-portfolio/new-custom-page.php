<?php
/**
 * Template Name: About me
 *
 * @package Developer_Portfolio
 */

get_header();
?>

<?php if ( have_posts() ) : ?>
	<?php
	while ( have_posts() ) :
		the_post();
		?>
	
	<h1><?php echo esc_html( get_the_title() ); ?></h1>
		<?php the_content(); ?>
	<?php endwhile; ?>
<?php endif; ?>

<?php get_footer(); ?>