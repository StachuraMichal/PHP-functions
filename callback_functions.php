<?php

function extract_images(&$post)
{
    $zip_file = get_field('zip', $post->ID);
    if (!$zip_file) {
        return;
    }
    $zip = new ZipArchive;
    $path = parse_url($zip_file['url'], PHP_URL_PATH);
    $files = array();
    $upload_dir = wp_upload_dir();
    if ($zip->open(getcwd() . $path) === true) {

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $filename = $zip->getNameIndex($i);
            $files[] = $filename;
        }
        $zip->extractTo('.' . pathinfo($path)['dirname'] . '/', $files);
    } else {
        return;
    }

    $dirs = scandir(getcwd() . pathinfo($path)['dirname'], 1);
    $abs_dir = getcwd() . pathinfo($path)['dirname'] . '/';
    $local_dir = pathinfo($files[0])['dirname'];
    foreach ($dirs as $dir) {
        if (is_dir($abs_dir . $dir)) {
            if ($local_dir == $dir) {
                if (strpos($dir, ' ') !== false) {
                    $new_dir = remove_accents(sanitize_file_name($dir));
                    rename($abs_dir . $dir, $abs_dir . $new_dir);
                    $local_dir = $new_dir;
                }
            }
        }
    }

    $accumulated_html = '';
    foreach ($files as $file) {
        $abs_path = $abs_dir . $local_dir . '/' . pathinfo($file)['basename'];
        $filename = basename($file);
        $wp_filetype = wp_check_filetype($filename, null);
        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => sanitize_file_name($filename),
            'post_content' => '',
            'post_status' => 'inherit',
        );
        $attach_id = wp_insert_attachment($attachment, $abs_path, $post->ID);
        require_once ABSPATH . 'wp-admin/includes/image.php';
        $attach_data = wp_generate_attachment_metadata($attach_id, $abs_path);
        wp_update_attachment_metadata($attach_id, $attach_data);
        $accumulated_html = $accumulated_html . ' <!-- wp:image {"id":' . $attach_id . ',"sizeSlug":"large"} -->
<figure class="wp-block-image size-large"><img src="' . wp_get_attachment_url($attach_id) . '" alt="" class="wp-image-' . $attach_id . '"/></figure>
<!-- /wp:image -->
';

    }
    $updated_post = array(
        'ID' => $post->ID,
        'post_content' => $accumulated_html,
    );
    update_post_meta($post->ID, 'zip', '');
    wp_update_post($updated_post);

}
?>
