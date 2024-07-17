<?php

if (!class_exists('MV_Translations_Post_Type')) {
    class MV_Translations_Post_Type
    {

        public function __construct()
        {
            add_action('init', array($this, 'create_post_type'));
            add_action('init', array($this, 'create_taxonomy'));
            add_action('init', array($this, 'register_metadata_table'));
            add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
            add_action('wp_insert_post', array($this, 'save_post'), 10, 2);
            add_action('delete_post', array($this, 'delete_post'));
        }

        public function create_post_type()
        {
            register_post_type(
                'mv-translations',
                array(
                    'label' => esc_html__('Translations', 'mv-translations'),
                    'description' => esc_html__('Translations', 'mv-translations'),
                    'labels' => array(
                        'name' => esc_html__('Translations', 'mv-translations'),
                        'singular_name' => esc_html__('Translation', 'mv-translations'),
                    ),
                    'public' => true,
                    'supports' => array('title', 'editor', 'author'),
                    'rewrite' => array(
                        'slug' => 'translations',
                    ),
                    'hierarchical' => false,
                    'show_ui' => true,
                    'show_in_menu' => true,
                    'menu_position' => 5,
                    'show_in_admin_bar' => true,
                    'show_in_nav_menus' => true,
                    'can_export' => true,
                    'has_archive' => true,
                    'exclude_from_search' => false,
                    'publicly_queryable' => true,
                    'show_in_rest' => true,
                    'menu_icon' => 'dashicons-welcome-write-blog',
                )
            );
        }

        public function create_taxonomy()
        {
            register_taxonomy(
                'singers',
                'mv-translations',
                array(
                    'labels' => array(
                        'name' => __('Singers', 'mv-translations'),
                        'singular_name' => __('Singer', 'mv-translations'),
                    ),
                    // Quando não é hierárquica, é categoria.
                    'hierarchical' => false,
                    'show_in_rest' => true,
                    'public' => true,
                    'show_admin_column' => true,
                )
            );
        }

        public function register_metadata_table()
        {
            global $wpdb;

            $wpdb->translationsmeta = $wpdb->prefix . "translationmeta";
        }

        public function add_meta_boxes()
        {
            add_meta_box(
                'mv_translations_meta_box',
                esc_html__('Translations Options', 'mv-translations'),
                array($this, 'add_inner_meta_box'),
                'mv-translations',
                'normal',
                'high'
            );
        }

        public function add_inner_meta_box($post)
        {
            require_once(MV_TRANSLATIONS_PATH . 'views/mv-translations_metabox.php');
        }

        public static function save_post($post_id, $post)
        {
            if (isset($_POST['mv_translations_nonce'])) {
                if (!wp_verify_nonce($_POST['mv_translations_nonce'], 'mv_translations_nonce')) {
                    return;
                }
            }

            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return;
            }

            if (isset($_POST['post_type']) && $_POST['post_type'] == 'mv-translations') {
                if (!current_user_can('edit_page', $post_id)) {
                    return;
                } elseif (!current_user_can('edit_post', $post_id)) {
                    return;
                }
            }

            if (isset($_POST['action']) && $_POST['action'] == 'editpost') {

                $transliteration = sanitize_text_field($_POST['mv_translations_transliteration']);
                $video = esc_url_raw($_POST['mv_translations_video_url']);

                global $wpdb;

                if ($_POST['mv_translations_action'] == 'save') {
                    if (
                        get_post_type($post) == 'mv-translations' &&
                        $post->post_status != 'trash' &&
                        $post->post_status != 'auto-draft' &&
                        $post->post_status != 'draft' &&
                        // get_var precisa de prepara para evitar SQL Injection, ao contrário de insert que já faz o sanitize
                        $wpdb->get_var($wpdb->prepare("SELECT translation_id FROM $wpdb->translationsmeta WHERE translation_id = %d", $post_id)) == null
                    ) {
                        $wpdb->insert(
                            $wpdb->translationsmeta,
                            array('translation_id' => $post_id, 'meta_key' => 'mv_translations_transliteration', 'meta_value' => $transliteration),
                            array('%d', '%s', '%s')
                        );

                        $wpdb->insert(
                            $wpdb->translationsmeta,
                            array('translation_id' => $post_id, 'meta_key' => 'mv_translations_video_url', 'meta_value' => $video),
                            array('%d', '%s', '%s')
                        );
                    }
                } else {
                    if (get_post_type($post) == 'mv-translations') {

                        $wpdb->update(
                            $wpdb->translationsmeta,
                            array('meta_value' => $transliteration),
                            array(
                                'translation_id' => $post_id,
                                'meta_key' => 'mv_translations_transliteration'
                            ),
                            array('%s'),
                            array('%d', '%s')

                        );

                        $wpdb->update(
                            $wpdb->translationsmeta,
                            array('meta_value' => $video),
                            array(
                                'translation_id' => $post_id,
                                'meta_key' => 'mv_translations_video_url'
                            ),
                            array('%s'),
                            array('%d', '%s')

                        );
                    }
                }
            }
        }

        public function delete_post($post_id)
        {
            if(!current_user_can('delete_posts')) {
                return;
            }

            if(get_post_type($post_id) == 'mv-translations') {
                global $wpdb;

                $wpdb->delete(
                    $wpdb->translationsmeta,
                    array('translation_id' => $post_id),
                    array('%d')
                );
            }
        }
    }
}
