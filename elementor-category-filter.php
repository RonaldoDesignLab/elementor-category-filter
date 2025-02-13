<?php
/**
 * Plugin Name: Elementor Category Filter Widget
 * Description: A simple Elementor widget that allows category-based filtering with AJAX.
 * Version: 1.0
 * Author: Your Name
 * Text Domain: elementor-category-filter
 */

// Ensure WordPress environment is loaded
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Register the widget with Elementor
function register_category_filter_widget( $widgets_manager ) {
    require_once( __DIR__ . '/category-filter-widget.php' );
    $widgets_manager->register( new \Elementor_Category_Filter_Widget() );
}
add_action( 'elementor/widgets/register', 'register_category_filter_widget' );

// Enqueue Scripts and Styles
function category_filter_widget_scripts() {
    wp_enqueue_script( 'jquery' );
    wp_enqueue_style( 'category-filter-style', plugin_dir_url( __FILE__ ) . 'style.css' );
}
add_action( 'wp_enqueue_scripts', 'category_filter_widget_scripts' );

// Filter posts by category for AJAX request
function filter_category_posts() {
    if (isset($_GET['cat']) && isset($_GET['post_type'])) {
        $category_id = sanitize_text_field($_GET['cat']); // Get the selected category ID
        $post_type = sanitize_text_field($_GET['post_type']); // Get the selected post type
        $show_all_categories = isset($_GET['show_all_categories']) && $_GET['show_all_categories'] === 'yes';

        // Initialize the WP_Query args
        $args = [
            'post_type' => $post_type,
            'posts_per_page' => 10, // Show a limited number of posts
            'post_status' => 'publish', // Ensure only published posts are shown
        ];

        // If "All Categories" is selected
        if ($category_id === 'all' || $show_all_categories) {
            // No category filter is applied, show all posts
        } else {
            // Filter by selected category
            $args['tax_query'] = [
                [
                    'taxonomy' => 'category',
                    'field' => 'id',
                    'terms' => $category_id,
                    'operator' => 'IN',
                ],
            ];
        }

        // Fetch posts based on the query
        $query = new WP_Query($args);

        // Output the posts
        if ($query->have_posts()) :
            while ($query->have_posts()) : $query->the_post();
                ?>
                <div class="post-item">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="post-thumbnail">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('medium'); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    <div class="ecf-post-content">
                    <h2><?php the_title(); ?></h2>
                        <div class="post-excerpt"><?php the_excerpt(); ?></div>
                        <a class="ecf-readmore" href="<?php the_permalink(); ?>">Read more</a>
                    </div>
                </div>
                <?php
            endwhile;
        else :
            echo '<p>No posts found in this category.</p>';
        endif;

        wp_reset_postdata();
    }

    wp_die(); // Terminate AJAX request
}
add_action('wp_ajax_filter_category_posts', 'filter_category_posts');
add_action('wp_ajax_nopriv_filter_category_posts', 'filter_category_posts');
