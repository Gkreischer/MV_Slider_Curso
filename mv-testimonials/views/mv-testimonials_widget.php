<?php

$testimonials = new WP_Query(
    array(
        'post_type' => 'mv-testimonials',
        'posts_per_page' => $number,
        'post_status' => 'publish',
    )
);

if ($testimonials->have_posts()) :
    while ($testimonials->have_posts()) :
        $testimonials->the_post();

        $url_meta = get_post_meta(get_the_ID(), 'mv_testimonials_user_url', true);
        $occupation_meta = get_post_meta(get_the_ID(), 'mv_testimonials_occupation', true);
        $company_meta = get_post_meta(get_the_ID(), 'mv_testimonials_company', true);


?>

        <div class="testimonial-item">
            <div class="title">
                <h3><?php the_title(); ?></h3>
            </div>
            <div class="content">
                <?php if ($image) : ?>
                    <div class="thumb">
                        <?php if (has_post_thumbnail()) {
                            the_post_thumbnail('thumbnail', array(70, 70));
                        } ?>
                    </div>
                <?php endif; ?>
                <?php the_content(); ?>
            </div>
            <div class="meta">
                <?php if ($occupation) : ?>
                    <span class="occupation">
                        <?php echo esc_html($occupation_meta); ?>
                    </span>
                <?php endif; ?>
                <?php if ($company) : ?>
                    <span class="company">
                        <!-- Necessário usar o rel assim, como descrito em  https://wpsamurai.com.br/o-que-e-relnoopener-e-qual-seu-impacto/ -->
                        <a href="<?php echo esc_url($url_meta); ?>" target="_blank" rel="noopener">
                            <?php echo esc_html($company_meta); ?>
                        </a>
                    </span>
                <?php endif; ?>
            </div>
        </div>

<?php
    endwhile;
    // Necessário após percorrer o loop para reset de $post
    wp_reset_postdata();
endif;
?>
<a href="<?php echo get_post_type_archive_link('mv-testimonials'); ?>"><?php echo esc_html_e('Show more testimonials', 'mv-testimonials'); ?></a>