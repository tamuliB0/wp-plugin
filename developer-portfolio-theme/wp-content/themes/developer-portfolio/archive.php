<?php
get_header();

if (have_posts()) :
    while (have_posts()) : the_post();
    ?>
    <?php get_template_part("template-parts/content");?>
    <?php endwhile;
else :
    _e("Sorry, no posts matched your criteria.", "developer-portfolio");
endif;

get_footer();
?>