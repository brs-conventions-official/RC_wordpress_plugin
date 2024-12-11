<?php
// Include topics and chemicals data
include_once plugin_dir_path(__FILE__) . 'topics.php';
include_once plugin_dir_path(__FILE__) . 'chemicals.php';

// Function to show Topics and Chemicals buttons for media
function brschm_show_media_options($media_id) {
    global $topics, $chemicals;

    // Your UI rendering code here (from Point 1)
}

// Add custom fields to Media Library
add_filter('attachment_fields_to_edit', function($form_fields, $post) {
    ob_start();
    brschm_show_media_options($post->ID); // Render the tagging UI
    $form_fields['chm_tagging'] = [
        'label' => 'CHM Tagging',
        'input' => 'html',
        'html'  => ob_get_clean(),
    ];
    return $form_fields;
}, 10, 2);

// Save custom fields for Media Library
add_filter('attachment_fields_to_save', function($post, $attachment) {
    if (isset($attachment['brschm_topics'])) {
        update_post_meta($post['ID'], '_brschm_topics', $attachment['brschm_topics']);
    }
    if (isset($attachment['brschm_chemicals'])) {
        update_post_meta($post['ID'], '_brschm_chemicals', $attachment['brschm_chemicals']);
    }
    return $post;
}, 10, 2);
