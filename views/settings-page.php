<!-- Classe padrão do Wordpress -->
<div class="wrap">
    <h1>
        <?= esc_html(get_admin_page_title()) ?>
    </h1>
    <!-- Cria novas abas dentro de configurações do Plugin -->
    <?php
        $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'main_options';
    ?>
    <h2 class="nav-tab-wrapper">
        <a href="?page=mv-slider-admin&tab=main_options" class="nav-tab <?= $active_tab == 'main_options' ? 'nav-tab-active' : '' ?>"><?php esc_html_e('Main Options', 'mv-slider'); ?></a>
        <a href="?page=mv-slider-admin&tab=additional_options" class="nav-tab <?= $active_tab == 'additional_options' ? 'nav-tab-active' : '' ?>"><?php esc_html_e('Additional Options', 'mv-slider'); ?></a>
    </h2>
    <!-- O Wordpress já oferece o options.php para salvar o form -->
    <!-- É possível verificar os options acessando /wp-admin/options.php -->
    <form action="options.php" method="post">
    <?php
        // Exibe as páginas correspondentes a aba selecionada
        if($active_tab == 'main_options') {
            settings_fields('mv_slider_group');
            do_settings_sections('mv_slider_page1');
        } else if($active_tab == 'additional_options') {
            settings_fields('mv_slider_group');
            do_settings_sections('mv_slider_page2');
        }
        submit_button(esc_html__('Save Settings', 'mv-slider'));
    ?>
    </form>
</div>