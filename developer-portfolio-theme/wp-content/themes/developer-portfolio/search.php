<?php
/**
 * Template for search page.
 *
 * @package Developer_Portfolio
 */

get_header();
?>
<h3>
	<?php echo esc_html( "Search results for  '" . get_search_query() . "'" ); ?>
</h3>
<?php
if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
		?>
		
		<h2>
			<a href="<?php echo esc_url( get_the_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
		</h2>
		<?php echo esc_html( get_the_date() ); ?>
		<?php
	endwhile;
	else :
		esc_html_e( 'No posts found', 'developer-portfolio' );
	endif;

	get_footer();
	?>