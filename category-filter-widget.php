<?php
// Ensure WordPress environment is loaded
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Elementor_Category_Filter_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'category_filter';
    }

    public function get_title() {
        return __( 'Category Filter', 'elementor-category-filter' );
    }

    public function get_icon() {
        return 'eicon-filter'; // Icon for the widget
    }

    public function get_categories() {
        return [ 'general' ]; // Category under which it appears
    }

    protected function _register_controls() {
        // Add control for selecting post type
        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'Category Filter Settings', 'elementor-category-filter' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        
    
        $this->add_control(
            'post_type',
            [
                'label' => __( 'Post Type', 'elementor-category-filter' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => $this->get_post_type_options(),
                'default' => 'post',
            ]
        );
    
        // Add control for selecting number of items per row
        $this->add_control(
            'posts_per_row',
            [
                'label' => __( 'Posts Per Row', 'elementor-category-filter' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 6,
                'step' => 1,
                'default' => 3,
            ]
        );
    
        // Add control for selecting row gap
        $this->add_control(
            'row_gap',
            [
                'label' => __( 'Row Gap', 'elementor-category-filter' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                        'step' => 5,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 20,
                ],
            ]
        );
    
        // Add control for selecting column gap
        $this->add_control(
            'column_gap',
            [
                'label' => __( 'Column Gap', 'elementor-category-filter' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                        'step' => 5,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 20,
                ],
            ]
        );
    
        // Add control for selecting All Categories option
        $this->add_control(
            'show_all_categories',
            [
                'label' => __( 'Show All Categories', 'elementor-category-filter' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __( 'Yes', 'elementor-category-filter' ),
                'label_off' => __( 'No', 'elementor-category-filter' ),
                'default' => 'yes',
            ]
        );
        $this->end_controls_section();


        // Style Section
    $this->start_controls_section(
        'style_section',
        [
            'label' => __( 'Style Settings', 'elementor-category-filter' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]
    );

    // Example: Control for customizing category filter link color
    $this->add_control(
        'link_color',
        [
            'label' => __( 'Link Color', 'elementor-category-filter' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .category-filter-widget a' => 'color: {{VALUE}};',
            ],
        ]
    );

    // Example: Control for customizing post item background
    $this->add_control(
        'post_background',
        [
            'label' => __( 'Post Background', 'elementor-category-filter' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .post-item' => 'background-color: {{VALUE}};',
            ],
        ]
    );

    $this->add_control(
        'custom_css',
        [
            'label' => __( 'Custom CSS', 'elementor-category-filter' ),
            'type' => \Elementor\Controls_Manager::CODE,
            'language' => 'css', // Optional, you can set it to 'css' to show CSS syntax highlighting
            'label_block' => true,
            'default' => '', // Default empty
        ]
    );
    $this->end_controls_section();
    
    }

    // Add style handling for your widget
        public function get_style() {
            ?>
            <style>
                .category-filter-widget {
                    display: flex;
                    flex-wrap: wrap;
                    gap: <?php echo esc_attr($this->get_settings_for_display('column_gap')); ?>px;
                }

                .category-filter-widget .post-item {
                    width: calc(100% / <?php echo esc_attr($this->get_settings_for_display('posts_per_row')); ?> - <?php echo esc_attr($this->get_settings_for_display('column_gap')); ?>px);
                    margin-bottom: <?php echo esc_attr($this->get_settings_for_display('row_gap')); ?>px;
                }

                .category-filter-widget .post-item {
                    width: calc(100% / <?php echo esc_attr($this->get_settings_for_display('posts_per_row')); ?> - <?php echo esc_attr($this->get_settings_for_display('column_gap')); ?>px);
                    margin-bottom: <?php echo esc_attr($this->get_settings_for_display('row_gap')); ?>px;
                }
            </style>
            <?php
        }
    
    // Render method inside category-filter-widget.php
    protected function render() {
        $settings = $this->get_settings_for_display();

        // Get the custom CSS
        $custom_css = !empty($settings['custom_css']) ? $settings['custom_css'] : '';
    
        // Output custom CSS if it's set
        if (!empty($custom_css)) {
            echo '<style>';
            echo esc_html($custom_css);
            echo '</style>';
        }
        // Get the selected number of posts per row
        $posts_per_row = !empty($settings['posts_per_row']) ? $settings['posts_per_row'] : 3;
        $row_gap = !empty($settings['row_gap']['size']) ? $settings['row_gap']['size'] : 20;
        $column_gap = !empty($settings['column_gap']['size']) ? $settings['column_gap']['size'] : 20;
        $show_all_categories = isset($settings['show_all_categories']) && $settings['show_all_categories'] === 'yes';

        // Fetch categories
        $categories = get_terms([
            'taxonomy' => 'category',
            'hide_empty' => false,
        ]);

        // Display category filter
        echo '<div class="category-filter-widget">';
        echo '<ul id="category-filter">';

        // "All Categories" option
        if ($show_all_categories) {
            echo '<li><a href="#" class="category-filter-link" data-category-id="all">' . __('All Categories', 'elementor-category-filter') . '</a></li>';
        }

        // Loop through categories and display them
        if (!empty($categories) && !is_wp_error($categories)) :
            foreach ($categories as $category) :
                echo '<li><a href="#" class="category-filter-link" data-category-id="' . esc_attr($category->term_id) . '">' . esc_html($category->name) . '</a></li>';
            endforeach;
        endif;

        echo '</ul>';
        echo '</div>';

        // Display the posts
        echo '<div id="posts-list" style="display: flex; flex-wrap: wrap; gap: ' . esc_attr($column_gap) . 'px; margin-bottom: ' . esc_attr($row_gap) . 'px;">';
        $this->display_posts($settings['post_type'], $show_all_categories);
        echo '</div>';

        // Enqueue AJAX script
        ?>
        <script>
       jQuery(document).ready(function($) {
    // Handle category selection click
    $('#category-filter a').on('click', function(e) {
        e.preventDefault();

        var categoryId = $(this).data('category-id'); // Get selected category ID
        var showAllCategories = categoryId === 'all' ? 'yes' : 'no'; // Set if "All Categories" is selected

        // Send AJAX request to filter posts by category
        $.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'GET',
            data: {
                action: 'filter_category_posts',
                cat: categoryId, // Pass the selected category ID
                post_type: '<?php echo $settings["post_type"]; ?>',
                show_all_categories: showAllCategories, // Pass if "All Categories" is selected
            },
            beforeSend: function() {
                $('#posts-list').html('<p>Loading posts...</p>');
            },
            success: function(response) {
                $('#posts-list').html(response); // Update posts
            }
        });
    });
});


        </script>
        <?php
    }
    
    // Display posts function
    private function display_posts($post_type, $show_all_categories) {
        $selected_category = isset( $_GET['cat'] ) ? $_GET['cat'] : '';

        $args = [
            'post_type' => $post_type,
            'posts_per_page' => 10, // Show a limited number of posts
        ];

        if ( ! empty( $selected_category ) && $selected_category !== 'all' && !$show_all_categories ) {
            $args['tax_query'] = [
                [
                    'taxonomy' => 'category',
                    'field' => 'id',
                    'terms' => $selected_category,
                    'operator' => 'IN',
                ],
            ];
        }

        $query = new WP_Query( $args );

        // Check if posts are found
        if ( $query->have_posts() ) :
            while ( $query->have_posts() ) : $query->the_post();
                ?>
                <div class="post-item">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="post-thumbnail">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('medium'); ?> <!-- Display the featured image -->
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

    // Get available post types for the select field
    private function get_post_type_options() {
        $post_types = get_post_types( [ 'public' => true ], 'objects' );
        $options = [];
        foreach ( $post_types as $post_type ) {
            $options[ $post_type->name ] = $post_type->label;
        }
        return $options;
    }
}

