<?php
/**
 * E-Shoppers functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package E-Shoppers
 */

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

if ( ! function_exists( 'e_shoppers_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function e_shoppers_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on E-Shoppers, use a find and replace
		 * to change 'e-shoppers' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'e-shoppers', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus(
			array(
				'menu-1' => esc_html__( 'Primary', 'e-shoppers' ),
			)
		);

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
			)
		);

		// Set up the WordPress core custom background feature.
		add_theme_support(
			'custom-background',
			apply_filters(
				'e_shoppers_custom_background_args',
				array(
					'default-color' => 'ffffff',
					'default-image' => '',
				)
			)
		);

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 250,
				'width'       => 250,
				'flex-width'  => true,
				'flex-height' => true,
			)
		);
	}
endif;
add_action( 'after_setup_theme', 'e_shoppers_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function e_shoppers_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'e_shoppers_content_width', 640 );
}
add_action( 'after_setup_theme', 'e_shoppers_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function e_shoppers_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'e-shoppers' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'e-shoppers' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'e_shoppers_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function e_shoppers_scripts() {
	wp_enqueue_style( 'e-shoppers-style', get_stylesheet_uri(), array(), _S_VERSION );
	wp_style_add_data( 'e-shoppers-style', 'rtl', 'replace' );

	wp_enqueue_script( 'e-shoppers-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'e_shoppers_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

/**
 * Create post type
 */
function custom_post_type() {
	$labels = array(
		'name'               => 'Clothes',
		'singular_name'      => 'clothes',
		'add_new'            => 'Add Clothes',
		'all_items'          => 'All Clothes',
		'add_new_item'       => 'Add Clothes',
		'edit_item'          => 'Edit Clothes',
		'new_item'           => 'New Clothes',
		'view_item'          => 'View Clothes',
		'search_item'        => 'Search Clothes',
		'not_found'          => 'No clothes found',
		'not_found_in_trash' => 'No clothes found in trash',
	);
	$args   = array(
		'labels'          => $labels,
		'public'          => true,
		'has_archive'     => true,
		'rewrite'         => true,
		'capability_type' => 'post',
		'menu_position'   => 4,
		'show_in_rest'    => true,
		'rewrite'         => array( 'slug' => 'clothes' ),
		'supports'        => array(
			'editor',
			'thumbnail',
			'excerpt',
			'title',
			'comments',
		),
	);
	register_post_type( 'clothes', $args );
}
add_action( 'init', 'custom_post_type' );


/**
 * Description: A short example showing how to add a taxonomy called Course.
 */
function register_taxonomy_course() {
	$labels = array(
		'name'              => _x( 'Type', 'taxonomy general name', 'types' ),
		'singular_name'     => _x( 'Type', 'taxonomy singular name', 'types' ),
		'search_items'      => __( 'Search Types' ),
		'all_items'         => __( 'All Types' ),
		'parent_item'       => __( 'Parent Types' ),
		'parent_item_colon' => __( 'Parent Types:' ),
		'edit_item'         => __( 'Edit Types' ),
		'update_item'       => __( 'Update Types' ),
		'view_item'         => __( 'View Types' ),
		'add_new_item'      => __( 'Add New Types' ),
		'seacrh_item'       => __( 'Search Types' ),
		'not_found'         => __( 'No types found' ),
		'new_item_name'     => __( 'New Types Name' ),
		'menu_name'         => __( 'Types' ),
	);
	$args   = array(
		'hierarchical'      => true, // make it hierarchical (like categories) .
		'labels'            => $labels,
		'public'            => true,
		'show_ui'           => true,
		'show_in_rest'      => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'types' ),
	);
	register_taxonomy( 'types', array( 'clothes' ), $args );

}

add_action( 'init', 'register_taxonomy_course', 0 );

/**
 * Function for adding custom text field in taxonomy
 */
function add_custom_field() {?>
	<div class="form-field">
		<label>Custom Field</label>
		<input type="text" name="field_text">
		<p>Enter a value for this field</p>
	</div>	
	<br><br>
	<?php
	?>
<?php
}
add_action('types_add_form_fields', 'add_custom_field');


/**
 * Function for saving custom text field in taxonomy
 */
function save_form_field($term_id, $taxonomy) {
	$value = $_POST['field_text'];
	add_term_meta($term_id , 'CPT_text_field', $value);
}
add_action('created_types', 'save_form_field', 10, 2);

/**
 * Function for editing custom text field in taxonomy
 */
function edit_custom_field($term_id) {
	$text = get_term_meta($term_id->term_id, 'CPT_text_field', true);	
	?>
	<tr class="form-field">
		<th>
			<label>Custom Field</label>
		</th>
		<td>
			<input type="text" name='field_text' value="<?php echo $text ?>">
			<p>Enter a value for this field</p>
		</td>
	</tr>
	<br><br>
<?php
}
add_action('types_edit_form_fields', 'edit_custom_field');


/**
 * Function for saving edited custom text field in taxonomy
 */
function save_edited_form_field($term_id, $taxonomy) {
	$value = $_POST['field_text'];
	update_term_meta($term_id, 'CPT_text_field', $value);
}
add_action('edited_types', 'save_edited_form_field', 10, 2);


/**
 * Function for adding custom image field in taxonomy
 */
function add_image_field() {?>
	<div class="form-field">
		<label>Image</label>
		<input type="text" name="upload_image" id="upload_image" value="" style="width: 77%"><br><br>
		<input type="button" id="upload_image_button" class="button" value="Upload an Image" />
	</div>	
	<br><br>
<?php
}
add_action('types_add_form_fields', 'add_image_field', 10);

/**
 * Function for saving custom image field in taxonomy
 */
function save_image_field($term_id, $taxonomy) {
	$image = $_POST['upload_image'];
    add_term_meta( $term_id, 'image_field', $image);
}
add_action('created_types', 'save_term_image', 10, 2);

/**
 * Function for editing custom image field in taxonomy
 */
function edit_image_field($term, $taxonomy) {
	$image = get_term_meta($term->term_id, 'image_field', true);
	?>
	<div class="form-field">
		<th>
			<label>Image</label>
		</th>
		<td>
			<input type="text" name="upload_image" id="upload_image" value="<?php echo $image ?>" style="width: 77%">
			<input type="button" id="upload_image_button" class="button" value="Upload an Image" />
		</td>
	</div>	
<?php	
}
add_action('types_edit_form_fields', 'edit_image_field', 10, 2);

/**
 * Function for saving edited custom image field in taxonomy
 */
function save_edited_image_field($term_id, $taxonomy) {
	$image = $_POST['upload_image'];
	update_term_meta($term_id, 'image_field', $image);
}
add_action('edited_types', 'save_edited_image_field', 10, 2);

/**
 * Function for enquing custom image field in taxonomy
 */
function image_uploader_enqueue() {
	wp_enqueue_media();

	wp_register_script( 'meta-image', get_template_directory_uri() . '/js/media-uploader.js', array( 'jquery' ) );
	wp_localize_script( 'meta-image', 'meta_image',
		array(
			'title' => 'Upload an Image',
			'button' => 'Use this Image',
		)
	);
	wp_enqueue_script( 'meta-image' );
}
add_action( 'admin_enqueue_scripts', 'image_uploader_enqueue' );





