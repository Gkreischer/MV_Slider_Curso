<!-- Classe padrão do Wordpress -->
<div class="wrap">
    <h1>
        <?= esc_html(get_admin_page_title()) ?>
    </h1>
    <!-- O Wordpress já oferece o options.php para salvar o form -->
    <!-- É possível verificar os options acessando /wp-admin/options.php -->
    <form action="options.php" method="post">
    <?php
        settings_fields('mv_slider_group');
        // Exibe as abas de configurações
        do_settings_sections('mv_slider_page1');
        do_settings_sections('mv_slider_page2');
        submit_button('Save Settings');
    ?>
    </form>
</div>