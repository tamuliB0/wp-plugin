<?php
/**
 * Main template file.
 *
 * @package Developer_Portfolio
 */
get_header();

if (have_posts()) :
    while (have_posts()) :
        the_post();
        ?>
        <h2><?php the_title();?></h2>
        <?php the_time();
        the_content();
        
        ?>
        <?php
    endwhile;
    else :
        _e("No posts found", "developer-portfolio");
    endif;

get_footer();
?>
