<?php
// Include media options rendering file
include_once plugin_dir_path(__FILE__) . 'includes/media-options.php';

// Add custom meta fields to the Media Library for CHM tagging
function brschm_add_media_fields($form_fields, $post) {
    // Add CHM checkbox
    $form_fields['chm_expose'] = [
        'label' => 'Expose as CHM Document',
        'input' => 'html',
        'html'  => '<input type="checkbox" name="attachments[' . $post->ID . '][chm_expose]" value="1" ' . (get_post_meta($post->ID, '_chm_expose', true) ? 'checked="checked"' : '') . '>',
    ];

    // Add Topics and Chemicals fields (rendered in media-options.php)
    ob_start();
    brschm_show_media_options($post->ID); // Render topics and chemicals UI
    $form_fields['chm_topics_chemicals'] = [
        'label' => 'Topics and Chemicals',
        'input' => 'html',
        'html'  => ob_get_clean(),
    ];

    return $form_fields;
}
add_filter('attachment_fields_to_edit', 'brschm_add_media_fields', 10, 2);

// Save the CHM metadata when media is updated
function brschm_save_media_fields($post, $attachment) {
    if (isset($attachment['chm_expose'])) {
        update_post_meta($post['ID'], '_chm_expose', 1);
    } else {
        delete_post_meta($post['ID'], '_chm_expose');
    }

    if (isset($attachment['chm_topics'])) {
        update_post_meta($post['ID'], '_chm_topics', $attachment['chm_topics']);
    } else {
        delete_post_meta($post['ID'], '_brschm_topics');
    }

    if (isset($attachment['chm_chemicals'])) {
        update_post_meta($post['ID'], '_chm_chemicals', $attachment['chm_chemicals']);
    } else {
        delete_post_meta($post['ID'], '_brschm_chemicals');
    }

    return $post;
}
add_filter('attachment_fields_to_save', 'brschm_save_media_fields', 10, 2);
