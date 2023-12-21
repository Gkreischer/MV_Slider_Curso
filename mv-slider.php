<?php

/**
 * Plugin Name: MV Slider
 * Plugin URI: https://gkdeveloper.com.br
 * Description: Plugin para gerenciamento de slides
 * Version: 1.0.0
 * Requires at leat: 5.6
 * Author: Gustavo Kreischer de Almeida
 * Author URI: https://gkdeveloper.com.br
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: mv-slider
 * Domain Path: /languages
 */

// Bloqueia execução externa
if (!defined('ABSPATH')) {
    exit;
}

if(!class_exists('MV_Slider')) {
    class MV_Slider {
        
        function __construct()
        {
            $this->define_constants();
            // Cria CPT
            require_once(MV_SLIDER_PATH . 'post-types/class.mv-slider-cpt.php');
            $MV_Slider_Post_Type = new MV_Slider_Post_Type();

            // Adicionando menu options para gerenciamento do plugin
            add_action('admin_menu', array($this, 'add_menu_options'));

            require_once(MV_SLIDER_PATH . 'class.mv-slider-settings.php');
            $MV_Slider_Settings = new MV_Slider_Settings();

            require_once(MV_SLIDER_PATH . 'shortcodes/class.mv-slider-shortcode.php');
            $MV_Slider_Shortcode = new MV_Slider_Shortcode();

            // Obriga o carregamento por último dos css e scripts
            add_action('wp_enqueue_scripts', array($this, 'register_scripts'), 999);

            // Agora carrega os arquivos no lado do admin
            add_action('admin_enqueue_scripts', array($this, 'register_admin_scripts'));
        }

        public function define_constants() 
        {
            define('MV_SLIDER_PATH', plugin_dir_path(__FILE__));
            define('MV_SLIDER_URL', plugin_dir_url(__FILE__));
            define('MV_SLIDER_VERSION', '1.0.0');
        }

        public static function activate()
        {
            // Refazer os links permanentes para evitar erros de CPT
            update_option('rewrite_rules', '');
        }

        public static function deactivate()
        {
            flush_rewrite_rules();
            // Remove CPT
            unregister_post_type('mv_slider');
        }

        public static function uninstall() {
            
        }

        public function add_menu_options()
        {
            // Referência para tipos de usuários e capacidades: https://wordpress.org/documentation/article/roles-and-capabilities/#capability-vs-role-table
            // Cria menu
            add_menu_page('MV Slider Options', 'MV Slider', 'manage_options', 'mv-slider-admin', array($this, 'mv_slider_settings_page'), 'dashicons-images-alt2');
            // Se usar add_plugins_page, incluimos um submenu dentro de plugins. Basta usar os mesmos argumentos do add_menu_page acima, removendo o ícone
            // Se usar add_theme_page, ele aparece em aparência
            // Se usar add_options_page, ele aparecerá em configurações

            // Adiciona o submenu Manage Slides dentro dentro de MV Slider
            add_submenu_page(
                'mv-slider-admin',
                'Manage Slides',
                'Manage Slides',
                'manage_options',
                // Aqui estamos passando a página do CPT previamente criada
                'edit.php?post_type=mv-slider',
                null,
                null
            );

            // Adiciona o submenu Add New Slide dentro dentro de MV Slider
            add_submenu_page(
                'mv-slider-admin',
                'Add New Slide',
                'Add New Slide',
                'manage_options',
                // Aqui estamos passando a página do CPT previamente criada
                'post-new.php?post_type=mv-slider',
                null,
                null
            );
        }

        public function mv_slider_settings_page()
        {
            // Verifica se o usuário tem permissão para acessar o painel
            if(!current_user_can('manage_options')) {
                return;
            }
            // Exibe mensagem de sucesso ao salvar
            if(isset($_GET['settings-updated'])) {
                add_settings_error('mv_slider_options', 'mv_slider_message', 'Settings Saved', 'success');
            }
            settings_errors('mv_slider_options');
            require_once(MV_SLIDER_PATH . 'views/settings-page.php');
        }

        public function register_scripts()
        {
            // Os dois últimos parâmetros são para dizer a dependência (Jquery) e se será carregado no head ou ao final do body, em </body>
            wp_register_script('mv-slider-main-jq', MV_SLIDER_URL . 'vendor/flexslider/jquery.flexslider-min.js', array('jquery'), MV_SLIDER_VERSION, true);
            wp_register_script('mv-slider-options-js', MV_SLIDER_URL . 'vendor/flexslider/flexslider.js', array('jquery'), MV_SLIDER_VERSION, true);
            // Registra o CSS. Último parâmetro é pra definir os media types (all, print, screen) ou media queries
            wp_register_style('mv-slider-css', MV_SLIDER_URL . 'vendor/flexslider/flexslider.css', array(), MV_SLIDER_VERSION, 'all');
        }

        public function register_admin_scripts()
        {
            // Usando a variável global typenow, verifica qual o tipo de CPT que estamos editando para evitar carregamento do CSS em outros CPTs/Posts. 
            //Também verifica se a edição está sendo feito em uma página ($typenow == 'page'), em uma postagem ($typenow == 'post') ou em um custom CTP ($typenow == 'custom-post-type')
            global $typenow;

            if($typenow == 'mv-slider'){
                wp_enqueue_style('mv-slider-admin', MV_SLIDER_URL . 'assets/css/admin.css', array(), MV_SLIDER_VERSION);
            }

            // Também poderiamos usar a variável global pagenow que verifica qual a página que está sendo editada. Por exemplo:
            // global $pagenow;
            // if('post.php' == $pagenow) {
            //     wp_enqueue_style('mv-slider-admin', MV_SLIDER_URL . 'assets/css/admin.css', array(), MV_SLIDER_VERSION);

            // }
        }

    }
}

if(class_exists('MV_Slider')) {
    // Hook para ativação do plugin. O segundo parâmetro precisa ser estático
    register_activation_hook(__FILE__, array('MV_Slider', 'activate'));
    // Hook para desativação do plugin. O segundo parâmetro precisa ser estático
    register_deactivation_hook(__FILE__, array('MV_Slider', 'deactivate'));
    // Hook para desinstalação do plugin. O segundo parâmetro precisa ser estático
    register_uninstall_hook(__FILE__, array('MV_Slider', 'uninstall'));
    $mv_slider = new MV_Slider();
}