<?php

if (!class_exists('MV_Slider_Post_Type')) {
    class MV_Slider_Post_Type
    {
        function __construct()
        {
            // Cria o CPT
            add_action('init', array($this, 'create_post_type'));
            // Cria metaboxes
            add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
            // Para salvar os posts com 2 parâmetros
            add_action('save_post', array($this, 'save_post'), 10, 2);
            // Padronizado sempre começando com manage_CPT_posts_columns. Aqui estamos adicionando colunas customizadas no CPT
            add_filter('manage_mv-slider_posts_columns', array($this, 'mv_slider_cpt_columns'));
            // Aqui estamos buscando as informações para preencher as colunas customizadas, com dois parâmetros
            add_action('manage_mv-slider_posts_custom_column', array($this, 'mv_slider_custom_columns'), 10, 2);
            // Permite ordenar as colunas customizadas
            add_filter('manage_edit-mv-slider_sortable_columns', array($this, 'mv_slider_sortable_columns'));
        }

        public function create_post_type()
        {
            register_post_type(
                'mv-slider',
                [
                    'label' => 'Slider',
                    'description' => 'Sliders',
                    'labels' => [
                        'name' => 'Sliders',
                        'singular_name' => 'Slider',
                    ],
                    'public' => true,
                    // O tema deve dar suporte ao uso de thumbnails, por exemplo
                    'supports' => ['title', 'editor', 'thumbnail'],
                    // Só irá funcionar se passar supports page-atributes. Aninha diferentes posts.
                    'hierarchical' => false,
                    // Relacionado ao public, mostra a área de edição do post
                    'show_ui' => true,
                    // Relacionado ao show_ui
                    'show_in_menu' => true,
                    'menu_position' => 5,
                    // Exibe a opção em New, na barra superior
                    'show_in_admin_bar' => true,
                    // Itens criados no CPT para adicionar ao menu da barra de navegação
                    'show_in_nav_menus' => true,
                    // Permite a exportação do Wordpress
                    'can_export' => true,
                    // Exibe todos os itens registrados no CPT em uma página do tipo blog
                    'has_archive' => false,
                    // Permite pesquisar o CPT
                    'exclude_from_search' => false,
                    // Permite consultas pelo frontend do CPT
                    'publicly_queryable' => true,
                    // Permite exibir o CPT no rest. É obrigatório para uso no editor de blocos Guttenberg que o valor seja true
                    'show_in_rest' => true,
                    // Icone para ser exibido no menu
                    'menu_icon' => 'dashicons-images-alt2',
                    // Método alternativo para criar metabox
                    // 'register_meta_box_cb' => array($this, 'add_meta_boxes'),
                ]
            );
        }

        public function add_meta_boxes()
        {
            add_meta_box(
                'mv_slider_meta_box',
                'Link Options',
                // método de callback
                array($this, 'add_inner_meta_boxes'),
                // Onde será exibido, passando uma chave do CPT
                'mv-slider',
                // Posição da caixa dentro da área de edição. Side, por exemplo, move para a barra lateral direita do post
                'normal',
                // Prioridade
                'high',
            );
        }

        public function add_inner_meta_boxes($post)
        {
            require_once(MV_SLIDER_PATH . 'views/mv-slider_metabox.php');
        }

        public function save_post($post_id)
        {
            // Verifica se o nonce foi enviado
            if (isset($_POST['mv_slider_nonce'])) {
                if (!wp_verify_nonce($_POST['mv_slider_nonce'], 'mv_slider_nonce')) {
                    return;
                }
            }

            // Desabilita o autosave do navegador
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return;
            }

            // Verifica se o user tem permissão e se é o CPT correto
            if (isset($_POST['post-type']) && $_POST['post-type'] == 'mv-slider') {
                if (!current_user_can('edit_post', $post_id)) {
                    return;
                } elseif (!current_user_can('edit_page', $post_id)) {
                    return;
                }
            }

            // Quando envia os dados do CPT, pega o action e verifica se é editpost para cadastro dos metaboxes
            if (isset($_POST['action']) && $_POST['action'] == 'editpost') {
                // Atributos de get_post_meta: post_id, name do input, se quiser retornar como array ou somente valor. Usar true para valor
                $old_link_text = get_post_meta($post_id, 'mv_slider_link_text', true);
                $new_link_text = $_POST['mv_slider_link_text'];
                $old_link_url = get_post_meta($post_id, 'mv_slider_link_url', true);
                $new_link_url = $_POST['mv_slider_link_url'];

                if (empty($new_link_text)) {
                    update_post_meta($post_id, 'mv_slider_link_text', 'Add some text');
                } else {
                    // Parâmetros: post_id, name do input, novo valor e valor antigo
                    update_post_meta($post_id, 'mv_slider_link_text', sanitize_text_field($new_link_text), $old_link_text);
                }

                if (empty($new_link_url)) {
                    update_post_meta($post_id, 'mv_slider_link_url', '#');
                } else {
                    // Parâmetros: post_id, name do input, novo valor e valor antigo
                    update_post_meta($post_id, 'mv_slider_link_url', esc_url_raw($new_link_url), $old_link_url);
                }
            }
        }

        public function mv_slider_cpt_columns($columns)
        {
            // Cria as colunas customizadas
            // Formato das colunas ($columns['campo_de_metadados'])
            $columns['mv_slider_link_text'] = esc_html__('Link Text', 'mv-slider');
            $columns['mv_slider_link_url'] = esc_html__('Link URL', 'mv-slider');

            return $columns;
        }

        public function mv_slider_custom_columns($column, $post_id)
        {
            // Busca os dados e exibe nas colunas customizadas do CPT
            switch($column) {
                case 'mv_slider_link_text':
                    echo esc_html(get_post_meta($post_id, 'mv_slider_link_text', true));
                    break;
                case 'mv_slider_link_url':
                    echo esc_url(get_post_meta($post_id, 'mv_slider_link_url', true));
                    break;
            }
        }

        public function mv_slider_sortable_columns($columns)
        {
            // Permite ordenas as colunas customizadas
            $columns['mv_slider_link_text'] = 'mv_slider_link_text';
            $columns['mv_slider_link_url'] = 'mv_slider_link_url';

            return $columns;
        }
    }
}
