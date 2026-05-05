<h1><?php the_title();?></h1>
<p><?php echo get_the_date();?></p>

<?php if (is_singular()) : ?>
    <?php the_content();?>
    <?php get_sidebar()?>
<?php else : ?>
    <?php the_excerpt();?>
<?php endif; ?>
        
<?php if (has_post_thumbnail()) : ?>
    <?php the_post_thumbnail("thumbnail"); ?>
<?php endif; ?>
