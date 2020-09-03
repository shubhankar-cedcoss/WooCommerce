<?php
get_header();
?>

<div class="content">
<?php
    $args = array(
        'post_type' => 'clothes',
        'post_status' => 'publish',
        'posts_per_page' => '3',
    );
    $loop = new WP_Query( $args );
     
        while ( $loop->have_posts() ) : $loop -> the_post();
   ?>
    <div class="thumbnail">
           <?php the_post_thumbnail(); ?>
    </div>
   <div class="title">
      <h2><a href="<?php the_permalink(); ?>"> <?php the_title(); ?></a></h2>
   </div>
   <div>
        <?php the_excerpt(); ?>
   </div>

   <?php
        endwhile;
        wp_reset_postdata()
    ?>
</div>
<?php
get_footer();