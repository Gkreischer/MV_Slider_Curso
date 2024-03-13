<!-- Content é o parâmetro do shortcode, é o texto que é utilizando entre os colchetes. Ex: [mv_slider]Lorem ipsum...[/mv_slider] -->
<h3><?php echo (!empty($content)) ? esc_html($content) : esc_html(MV_Slider_Settings::$options['mv_slider_title']); ?></h3>
<div class="mv-slider flexslider <?= isset(MV_Slider_Settings::$options['mv_slider_style']) ? esc_attr(MV_Slider_Settings::$options['mv_slider_style']) : ''; ?>">
    <ul class="slides">
        <?php

        $args = [
            'post_type' => 'mv-slider',
            'posts_status' => 'publish',
            // Define quais são as ids do slideshow, passando um array
            'post__in' => $id,
            'orderby' => $orderby,
        ];

        $my_query = new WP_Query($args);

        if($my_query->have_posts()):
            while($my_query->have_posts()) : $my_query->the_post();

            // Obtem os textos e os links para cada botão do slide
            $button_text = get_post_meta(get_the_ID(), 'mv_slider_link_text', true);
            $button_url = get_post_meta(get_the_ID(), 'mv_slider_link_url', true);

        ?>
        <li>
            
            <?php 
            // Verifica se uma imagem foi definida no slider
            if(has_post_thumbnail()) {
                the_post_thumbnail('full', ['class' => 'img-fluid']); 
            } else {
                echo mv_slider_get_placeholder_image();
            }
            ?>
            <div class="mvs-container">
                <div class="slider-details-container">
                    <div class="wrapper">
                        <div class="slider-title">
                            <h2><?= the_title(); ?></h2>
                        </div>
                        <div class="slider-description">
                            <div class="subtitle"><?= the_content(); ?></div>
                            <a class="link" href="<?= esc_url($button_url); ?>"><?= esc_html($button_text); ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </li>
        <?php
            endwhile;
            // Limpa a consulta para não afetar outras consultas que sejam feitas na página
            wp_reset_postdata();
        endif;
        ?>
    </ul>
</div>