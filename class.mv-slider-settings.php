<?php

if (!class_exists('MV_Slider_Settings')) {
    class MV_Slider_Settings
    {
        // Para acessar esse atributo externamente sem precisar instanciar a classe
        public static $options;

        public function __construct()
        {
            // Como é estático, usamos o self
            self::$options = get_option('mv_slider_options');

            add_action('admin_init', array($this, 'admin_init'));
        }

        // Criando as seções
        public function admin_init()
        {

            register_setting(
                'mv_slider_group',
                'mv_slider_options',
                [
                    $this, 'mv_slider_validate'
                ]
            );

            add_settings_section(
                'mv_slider_main_section',
                'How does it work?',
                null,
                'mv_slider_page1'
            );

            add_settings_section(
                'mv_slider_second_section',
                'Other Plugin Options',
                null,
                'mv_slider_page2'
            );

            add_settings_field(
                'mv_slider_shortcode',
                'Shortcode',
                array($this, 'mv_slider_shortcode_callback'),
                'mv_slider_page1',
                'mv_slider_main_section',
            );

            add_settings_field(
                'mv_slider_title',
                'Slider Title',
                array($this, 'mv_slider_title_callback'),
                'mv_slider_page2',
                'mv_slider_second_section',
                [
                    'label_for' => 'mv_slider_title',
                ]
            );

            add_settings_field(
                'mv_slider_bullets',
                'Display Bullets',
                array($this, 'mv_slider_bullets_callback'),
                'mv_slider_page2',
                'mv_slider_second_section',
                [
                    'label_for' => 'mv_slider_bullets',
                ]
            );

            add_settings_field(
                'mv_slider_style',
                'Slider Style',
                array($this, 'mv_slider_style_callback'),
                'mv_slider_page2',
                'mv_slider_second_section',
                // Podemos passar parâmetros diretamente daqui, caso necessário, na callback. No caso estamos usando $args
                [
                    'items' => [
                        'style-1',
                        'style-2',
                    ],
                    'label_for' => 'mv_slider_style',
                ]
            );
        }

        public function mv_slider_shortcode_callback()
        {
        ?>
            <span>Use the shortcode [mv_slider] to display the slider in any page/post/widget</span>
        <?php
        }

        public function mv_slider_title_callback($args)
        {
            ?>
                <input type="text" id="mv_slider_title" name="mv_slider_options[mv_slider_title]" value="<?php echo isset(self::$options['mv_slider_title']) ? esc_attr(self::$options['mv_slider_title']) : ''; ?>">
            <?php
        }

        public function mv_slider_bullets_callback($args)
        {
            ?>
                <input type="checkbox" id="mv_slider_bullets" name="mv_slider_options[mv_slider_bullets]" value="1" <?php 
                if(isset(self::$options['mv_slider_bullets'])) {
                    checked(1, self::$options['mv_slider_bullets'], true); 
                }
                ?>/>
                <label for ="mv_slider_bullets">Whether to display bullets or not</label>
            <?php
        }

        public function mv_slider_style_callback($args){
            ?>
            <select 
                id="mv_slider_style" 
                name="mv_slider_options[mv_slider_style]">
                <?php
                    foreach($args['items'] as $item) {
                        ?>
                            <option 
                                value="<?php echo esc_attr($item); ?>" 
                                <?php
                                    isset(self::$options['mv_slider_style']) ? selected($item, self::$options['mv_slider_style']) : '';
                                ?>
                            >
                                <?php echo ucfirst($item); ?>
                            </option>
                        <?php
                    }

                ?>
            </select>
            <?php
        }

        public function mv_slider_validate($input)
        {
            $new_input = [];

            foreach($input as $key => $value)
            {
                // Jeito genérico de resolver, porém só resolve para campos do tipo strings
                // $new_input[$key] = sanitize_text_field($value);

                switch($key) {
                    case 'mv_slider_title':
                        if(empty($value)) {
                            $value = 'Please, type some text';
                        }
                        $new_input[$key] = sanitize_text_field($value);
                        break;

                    // Exemplos de cada tipo de validação: URL e int
                    // case 'mv_slider_url':
                    //     $new_input[$key] = esc_url_raw($value);
                    //     break;
                    
                    // case 'mv_slider_int':
                    //     $new_input[$key] = absint($value);
                    //     break;
                    
                    default:
                        $new_input[$key] = sanitize_text_field($value);
                        break;
                }

            }

            return $new_input;
        }
    }
}
