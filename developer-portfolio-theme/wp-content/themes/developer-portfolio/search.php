<?php
get_header();
?>
<h3>Search results for <?php echo "'" . get_search_query() . "'"?></h3>
<?php 
if (have_posts()) :
    while (have_posts()) : the_post();
        ?>
        
        <h2>
            <a href="<?php the_permalink()?>"><?php the_title();?></a>
        </h2>
        <?php echo get_the_date();
        
        ?>
        <?php
    endwhile;
    else :
        _e("No posts found", "developer-portfolio");
    endif;
    
get_footer();
?>