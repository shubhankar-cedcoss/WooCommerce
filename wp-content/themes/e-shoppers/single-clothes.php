<?php
get_header();

if(have_posts()) {
    while(have_posts()) {
        the_post();                
    }
}

?>
<?php the_post_thumbnail(); ?>
<h2><?php the_title(); ?></h2>
<p><?php the_content(); ?></p>