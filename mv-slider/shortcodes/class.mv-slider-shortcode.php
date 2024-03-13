<?php

if (!class_exists('MV_Slider_Shortcode')) {
    class MV_Slider_Shortcode
    {

        public function __construct()
        {
            add_shortcode('mv_slider', array($this, 'add_shortcode'));
        }

        // Content é o texto que fica dentro do shortcode
        public function add_shortcode($atts = [], $content = null, $tag = '')
        {

            // Converte os atributos para letras minúsculas
            $atts = array_change_key_case((array) $atts, CASE_LOWER);

            // Extract transforma cada item do array em uma variável
            extract(
                // Mescla os valores padrão de um shortcode com os valores fornecidos pelo o usuário. O primeiro parâmetro são os parâmetros com valores default e 
                // o segundo $atts são informados pelo usuário
                shortcode_atts(
                    [
                        'id' => '',
                        'orderby' => 'date'
                    ],
                    $atts,
                    $tag
                )
            );

            if(!empty($id)){
                // Array map executa a função absint para cada valor de explode(',', $id) que é um array, nesse caso validando se os ids são inteiros
                $id = array_map('absint', explode(',', $id));
            }

            ob_start();
            // Usamos somente require porque se o shortcode for incluido mais de uma vez no mesmo post causará erro
            require(MV_SLIDER_PATH . 'views/mv-slider_shortcode.php');
            // Carrega os scripts e o CSS quando o shortcode for carregado
            wp_enqueue_script('mv-slider-main-jq');
            wp_enqueue_style('mv-slider-main-css');
            wp_enqueue_style('mv-slider-style-css');
            // Chama o arquivo JS passando parâmetros do PHP
            mv_slider_options();
            return ob_get_clean();
        }
    }
}
