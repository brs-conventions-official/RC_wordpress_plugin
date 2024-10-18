<?php
// Add the 'Language' field to attachment details
function add_language_field_to_attachments($form_fields, $post) {
    // Add the field to the attachment form
    $form_fields['attachment_language'] = array(
        'label' => __('Language'),
        'input' => 'text', // This is a text input field
        'value' => get_post_meta($post->ID, 'attachment_language', true), // Get the current value
        'helps' => __('Enter the language code (e.g., EN, FR)'),
    );

    return $form_fields;
}
add_filter('attachment_fields_to_edit', 'add_language_field_to_attachments', 10, 2);

// Save the 'Language' field when the attachment is updated
function save_language_field_for_attachments($post, $attachment) {
    // Check if the language field is set and save it
    if (isset($attachment['attachment_language'])) {
        update_post_meta($post['ID'], 'attachment_language', sanitize_text_field($attachment['attachment_language']));
    } else {
        delete_post_meta($post['ID'], 'attachment_language');
    }

    return $post;
}
add_filter('attachment_fields_to_save', 'save_language_field_for_attachments', 10, 2);
