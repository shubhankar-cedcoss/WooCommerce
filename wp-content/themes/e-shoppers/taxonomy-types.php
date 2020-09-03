<?php
get_header();

$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
?>
<h1 class="page-title">
<?php the_post_thumbnail(); ?><br>
<?php printf( __( 'Type of Cloth: %s'), '<span>' . $term->name . '</span>' );?>
</h1>
