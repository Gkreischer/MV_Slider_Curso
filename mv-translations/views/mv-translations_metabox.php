<?php
    global $wpdb;

    // Por segurança, sempre usar esc_sql para evitar SQL Injection
    // $query = "SELECT * FROM $wpbd->translationsmeta WHERE translation_id = '" . esc_sql($post->ID) . "'";
    // ou
    $query = $wpdb->prepare("SELECT * FROM $wpdb->translationsmeta WHERE translation_id = %d", get_the_ID());
    // OBJECT, OBJECT_K, ARRAY_N, ARRAY_A
    $results = $wpdb->get_results($query, ARRAY_A);
    // print_r($results);
?>

<table class="form-table mv-translations-metabox"> 
    <!-- Nonce -->
    <input type="hidden" name="mv_translations_nonce" value="<?php echo wp_create_nonce( 'mv_translations_nonce' ); ?>">

    <!-- Criado para verificar se é um create ou ou update   -->
     <input type="hidden" name="mv_translations_action" value="<?php echo ( empty( $results[0]['meta_value'] ) || empty($results[1]['meta_value']) ) ? 'save' : 'update'; ?>">
    <tr>
        <th>
            <label for="mv_translations_transliteration"><?php esc_html_e( 'Has transliteration?', 'mv-translations' ); ?></label>
        </th>
        <td>
            <select name="mv_translations_transliteration" id="mv_translations_transliteration">
                <option value="Yes" <?php isset( $results[0]['meta_value'] ) ? selected( $results[0]['meta_value'], 'Yes' ) : ''; ?>><?php esc_html_e( 'Yes', 'mv-translations' )?></option>';
                <option value="No" <?php isset( $results[0]['meta_value'] ) ? selected( $results[0]['meta_value'], 'No' ) : ''; ?>><?php esc_html_e( 'No', 'mv-translations' )?></option>';
            </select>            
        </td>
    </tr>
    <tr>
        <th>
            <label for="mv_translations_video_url"><?php esc_html_e( 'Video URL', 'mv-translations' ); ?></label>
        </th>
        <td>
            <input 
                type="url" 
                name="mv_translations_video_url" 
                id="mv_translations_video_url" 
                class="regular-text video-url"
                value="<?php echo ( isset( $results[1]['meta_value'] ) ) ? esc_url( $results[1]['meta_value'] ) : ""; ?>"
            >
        </td>
    </tr> 
</table>