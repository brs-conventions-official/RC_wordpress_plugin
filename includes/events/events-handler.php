<?php
// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Function to display event fields in the post editor
function display_event_fields($post) {
    if (isset($post->ID)  && get_post_type($post->ID) === 'post' && has_tag('event', $post->ID)) {
    $fields = [
        'event_name' => 'Event Name',
        'event_description' => 'Description',
        'event_status' => 'Status',
        'event_category' => 'Category',
        'event_country' => 'Country',
        'event_city' => 'City',
        'event_address' => 'Address',
        'event_contact' => 'Contact',
        'event_updated' => 'Updated',
        'event_url' => 'Event URL',
        'event_startdate' => 'Start Date',
        'event_enddate' => 'End Date',
    ];

    foreach ($fields as $key => $label) {
        $value = get_post_meta($post->ID, $key, true);
        if ($key === 'event_description') {
            echo "<label for='{$key}'>{$label}</label>";
            echo "<textarea id='{$key}' name='{$key}'>{$value}</textarea><br>";
        } else {
            echo "<label for='{$key}'>{$label}</label>";
            echo "<input type='text' id='{$key}' name='{$key}' value='{$value}'><br>";
        }
    }
}
}


// Function to save event data when the post is saved
function save_event_data($post_id) {
    // Check for autosave and permissions
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    // Save each field
    $fields = [
        'event_name', 'event_description', 'event_status', 'event_category', 'event_country',
        'event_city', 'event_address', 'event_contact', 'event_updated', 'event_url',
        'event_startdate', 'event_enddate'
    ];

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
        }
    }
}
// Hook to save event data on post save
add_action('save_post', 'save_event_data');