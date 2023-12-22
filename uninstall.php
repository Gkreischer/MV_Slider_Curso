<?php

// Restrição de acesso externo
if(!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

// Remove as opções de configuração do plugin
delete_option('mv_slider_options');

// Remove CPT
$posts = get_posts(
    array(
    'post_type' => 'mv-slider', 
    // Para trazer todos os slides, independente da quantidade
    'numberposts' => -1,
    'post_status' => 'any'
    )
);

foreach ($posts as $post) {
    wp_delete_post($post->ID, true);
}