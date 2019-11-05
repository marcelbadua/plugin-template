<?php
if (!class_exists('PLUGIN_TEMPLATE_PLUGIN_CONSTRUCT')) {

    class PLUGIN_TEMPLATE_PLUGIN_CONSTRUCT
    {
        const POST_TYPE = "plugin_template";

        public function __construct()
        {
            add_action('init', array( &$this, 'init' ));
            add_action('admin_init', array( &$this, 'admin_init' ));
        }
        public function init()
        {
            $this->create_post_type( self::POST_TYPE, true, false );

            $this->create_taxonomy( self::POST_TYPE . '_category', self::POST_TYPE );

            add_action('template_redirect', array( &$this, 'my_theme_redirect' ));

            add_action('wp_enqueue_scripts', array(&$this, 'my_script_enqueue'));

            if ( version_compare($GLOBALS['wp_version'], '5.0-beta', '>') ) {
                add_filter( 'use_block_editor_for_post_type', array(&$this, 'my_disable_gutenberg_for_post_type'), 10, 2 ); // WP > 5 beta
            } else {
                add_filter( 'gutenberg_can_edit_post_type', array(&$this, 'my_disable_gutenberg_for_post_type'), 10, 2 ); // WP < 5 beta
            };

        }

        public function my_disable_gutenberg_for_post_type( $is_enabled, $post_type ) {
            if ( self::POST_TYPE == $post_type ) {  // disable for pages, change 'page' to you CPT slug
                return false;
            }
            return $is_enabled;
        }

        public function my_script_enqueue()
        {
            wp_enqueue_style(
                'plugin_template',
                plugins_url('dist/css/plugin_template.css', dirname(__FILE__) ),
                array(),
                PLUGIN_TEMPLATE_PLUGIN_VERSION,
                'all'
            );

            wp_enqueue_script(
                'plugin_template',
                plugins_url('dist/js/plugin_template.js', dirname(__FILE__) ),
                array('jquery'),
                PLUGIN_TEMPLATE_PLUGIN_VERSION,
                TRUE  
                );

            wp_localize_script(
                'plugin_template',
                'plugin_template',
                array(
                    'nonce' => wp_create_nonce('plugin_template_nonce'),
                    'url'   => admin_url('admin-ajax.php'),
                )
            );
        }
        public function create_taxonomy($category, $post_type)
        {
            register_taxonomy($category, $post_type, array(
                'labels'                => array(
                    'name'                => __(sprintf('%s', ucwords(str_replace("_", " ", $category)))),
                    'add_new_item'        => __(sprintf('Add New %s', ucwords(str_replace("_", " ", $category)))),
                    'new_item_name'       => __(sprintf('New %s', ucwords(str_replace("_", " ", $category)))),
                ),
                'show_ui'               => true,
                'hierarchical'          => true,
                //'rewrite'               => array( 'slug' => sprintf('%s', ucwords(str_replace("_", " ", $category))) ),
                'show_admin_column'     => true,
                'query_var'             => true,
                'show_in_rest'          => true,
                'rest_base'             => $category,
            ));
        }

        public function create_post_type($post_type, $show_in_menu, $exclude_from_search)
        {
            register_post_type($post_type, array(
                'labels' => array(
                    'name' => _x(sprintf('%ss', ucwords(str_replace("_", " ", $post_type))), 'Post Type General Name', 'text_domain'),
                    'singular_name' => _x(sprintf('%s', ucwords(str_replace("_", " ", $post_type))), 'Post Type Singular Name', 'text_domain'),
                    'menu_name' => __(sprintf('%s', ucwords(str_replace("_", " ", $post_type))), 'text_domain'),
                    'name_admin_bar' => __(sprintf('%s', ucwords(str_replace("_", " ", $post_type))), 'text_domain'),
                    'archives' => __(sprintf('%s Archives', ucwords(str_replace("_", " ", $post_type))), 'text_domain'),
                    'parent_item_colon' => __(sprintf('Parent %s', ucwords(str_replace("_", " ", $post_type))), 'text_domain'),
                    'all_items' => __(sprintf('All %s', ucwords(str_replace("_", " ", $post_type))), 'text_domain'),
                    'add_new_item' => __(sprintf('Add New %s', ucwords(str_replace("_", " ", $post_type))), 'text_domain'),
                    'add_new' => __('Add New', 'text_domain'),
                    'new_item' => __(sprintf('New %s', ucwords(str_replace("_", " ", $post_type))), 'text_domain'),
                    'edit_item' => __(sprintf('Edit %s', ucwords(str_replace("_", " ", $post_type))), 'text_domain'),
                    'update_item' => __(sprintf('Update %s', ucwords(str_replace("_", " ", $post_type))), 'text_domain'),
                    'view_item' => __(sprintf('View %s', ucwords(str_replace("_", " ", $post_type))), 'text_domain'),
                    'search_items' => __(sprintf('Search %s', ucwords(str_replace("_", " ", $post_type))), 'text_domain'),
                    'not_found' => __('Not found', 'text_domain'),
                    'not_found_in_trash' => __('Not found in Trash', 'text_domain'),
                    'featured_image' => __('Featured Image', 'text_domain'),
                    'set_featured_image' => __('Set featured image', 'text_domain'),
                    'remove_featured_image' => __('Remove featured image', 'text_domain'),
                    'use_featured_image' => __('Use as featured image', 'text_domain'),
                    'insert_into_item' => __('Insert into item', 'text_domain'),
                    'uploaded_to_this_item' => __('Uploaded to this item', 'text_domain'),
                    'items_list' => __('Items list', 'text_domain'),
                    'items_list_navigation' => __('Items list navigation', 'text_domain'),
                    'filter_items_list' => __('Filter items list', 'text_domain'),
                ),
                'public' => true,
                'has_archive' => true,
                'supports' => array(
                    'title',
                    'thumbnail',
                    'editor',
                    'excerpt'
                ),
                'menu_icon' => 'dashicons-star-filled',
                'show_in_menu' => $show_in_menu,
                'exclude_from_search' => $exclude_from_search,
                'show_in_rest' => true
            ));
        }

        public function my_theme_redirect()
        {
            global $wp_query;

            if ( isset($wp_query->query_vars['taxonomy'])) {

                $taxonomy = $wp_query->query_vars['taxonomy'];

                $templatefilename = 'archive-'.$taxonomy.'.php';

                if (file_exists(TEMPLATEPATH . '/' . $templatefilename)) {
                    $return_template = TEMPLATEPATH . '/' . $templatefilename;
                } else {
                    $return_template = sprintf("%1s/../tpl/%2s", dirname(__FILE__), $templatefilename);
                }
                $this->do_theme_redirect($return_template);
            }

            if ($wp_query->query_vars['post_type'] == self::POST_TYPE) {
                if (is_archive()) {
                    $templatefilename = 'archive-' . self::POST_TYPE . '.php';
                } else {
                    $templatefilename = 'single-' . self::POST_TYPE . '.php';
                }
                if (file_exists(TEMPLATEPATH . '/' . $templatefilename)) {
                    $return_template = TEMPLATEPATH . '/' . $templatefilename;
                } else {
                    $return_template = sprintf("%1s/../tpl/%2s", dirname(__FILE__), $templatefilename);
                }

                $this->do_theme_redirect($return_template);

            }
        }
        public function do_theme_redirect($url)
        {
            global $post, $wp_query;
            if (have_posts()) {
                include $url;
                die();
            } else {
                $wp_query->is_404 = true;
            }
        }
        public function admin_init()
        {
            add_filter('enter_title_here', array( &$this, 'change_title_text' ));
        }
        public function change_title_text($title)
        {
            $screen = get_current_screen();
            if (self::POST_TYPE == $screen->post_type) {
                $title = __(sprintf('Enter %s Here', ucwords(str_replace("_", " ", self::POST_TYPE))), 'text_domain');
            }
            return $title;
        }

    }
}
