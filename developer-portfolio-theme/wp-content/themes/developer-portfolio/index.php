<?php
/**
 * Main template file.
 *
 * @package Developer_Portfolio
 */

get_header();

if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
		?>
		
		<h2>
			<a href="<?php echo esc_url( get_the_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
		</h2>
		<?php
		echo esc_html( get_the_date() );

		?>
		<?php
	endwhile;
	else :
		esc_html_e( 'No posts found', 'developer-portfolio' );
	endif;

	get_footer();
	?>
