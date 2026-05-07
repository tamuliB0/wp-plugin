<?php
/* 
Template Name: custom-page
*/   
get_header()?>
<?php $paged = get_query_var( "paged" ) ? get_query_var( "paged" ) : 1;?>
<?php $args = array(
    "post_type" => "post",
    "posts_per_page" => 5,
    "category_name" => "portfolio",
    "paged" => $paged,
    "orderby" => "title"
);
$query = new WP_Query($args);
?>

<?php if ($query->have_posts()) : ?>
    <div class="grid-container">
        <?php while ($query->have_posts()) : $query->the_post(); ?>
        
        <h2><?php the_title(); ?></h2>
        <?php if (has_post_thumbnail()) : ?>
            <?php the_post_thumbnail("thumbnail"); ?>
        <?php endif; ?>
        <?php endwhile; ?>
    </div>

    <?php wp_reset_postdata(); ?>

    <?php if ($query->max_num_pages > 1) : ?>
        <?php previous_posts_link("Previous"); ?>
        <?php next_posts_link("Next", $query->max_num_pages); ?>
    <?php endif; ?>

<?php else : ?>
    <?php _e("No projects found", "developer-portfolio"); ?>
<?php endif; ?>

<?php get_footer(); ?>