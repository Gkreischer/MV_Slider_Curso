<!-- Classe padrão do Wordpress -->
<div class="wrap">
    <h1>
        <?= esc_html(get_admin_page_title()) ?>
    </h1>
    <!-- O Wordpress já oferece o options.php para salvar o form -->
    <!-- É possível verificar os options acessando /wp-admin/options.php -->
    <form action="option.php" method="post">

    </form>
</div>