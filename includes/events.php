<?php
// Register the meta box for Events
function brschm_register_event_meta_box() {
    add_meta_box(
        'brschm_event_meta_box', // Meta box ID
        'Event Details',         // Title of the meta box
        'brschm_render_event_meta_box',  // Callback function to display the content
        'post',                  // Post type
        'normal',                // Context (normal, side, etc.)
        'high'                   // Priority
    );
}
add_action('add_meta_boxes', 'brschm_register_event_meta_box');

// Render the meta box content
function brschm_render_event_meta_box($post) {
    // Add a nonce field for security
    wp_nonce_field('brschm_save_event_meta_box', 'brschm_event_nonce');

    // Get existing event field values (if any)
    $fields = [
        'event_status' => get_post_meta($post->ID, '_brschm_event_status', true),
        'event_category' => get_post_meta($post->ID, '_brschm_event_category', true),
        'event_country' => get_post_meta($post->ID, '_brschm_event_country', true),
        'event_city' => get_post_meta($post->ID, '_brschm_event_city', true),
        'event_address' => get_post_meta($post->ID, '_brschm_event_address', true),
        'event_contact' => get_post_meta($post->ID, '_brschm_event_contact', true),
        'event_url' => get_post_meta($post->ID, '_brschm_event_url', true),
        'event_startdate' => get_post_meta($post->ID, '_brschm_event_startdate', true),
        'event_enddate' => get_post_meta($post->ID, '_brschm_event_enddate', true)
    ];

    ?>
    <p>
        <label for="brschm_event_status">Event Status:</label>
        <input type="text" id="brschm_event_status" name="brschm_event_status" value="<?php echo esc_attr($fields['event_status']); ?>" placeholder="Enter event status" />
    </p>
    <p>
        <label for="brschm_event_category">Event Category:</label>
        <input type="text" id="brschm_event_category" name="brschm_event_category" value="<?php echo esc_attr($fields['event_category']); ?>" placeholder="Enter event category" />
    </p>
    <p>
        <label for="brschm_event_country">Country:</label>
        <input type="text" id="brschm_event_country" name="brschm_event_country" value="<?php echo esc_attr($fields['event_country']); ?>" placeholder="Enter event country" />
    </p>
    <p>
        <label for="brschm_event_city">City:</label>
        <input type="text" id="brschm_event_city" name="brschm_event_city" value="<?php echo esc_attr($fields['event_city']); ?>" placeholder="Enter event city" />
    </p>
    <p>
        <label for="brschm_event_address">Address:</label>
        <input type="text" id="brschm_event_address" name="brschm_event_address" value="<?php echo esc_attr($fields['event_address']); ?>" placeholder="Enter event address" />
    </p>
    <p>
        <label for="brschm_event_contact">Contact:</label>
        <input type="text" id="brschm_event_contact" name="brschm_event_contact" value="<?php echo esc_attr($fields['event_contact']); ?>" placeholder="Enter event contact" />
    </p>
    <p>
        <label for="brschm_event_url">Event URL:</label>
        <input type="url" id="brschm_event_url" name="brschm_event_url" value="<?php echo esc_attr($fields['event_url']); ?>" placeholder="Enter event URL" />
    </p>
    <p>
        <label for="brschm_event_startdate">Start Date:</label>
        <input type="date" id="brschm_event_startdate" name="brschm_event_startdate" value="<?php echo esc_attr($fields['event_startdate']); ?>" />
    </p>
    <p>
        <label for="brschm_event_enddate">End Date:</label>
        <input type="date" id="brschm_event_enddate" name="brschm_event_enddate" value="<?php echo esc_attr($fields['event_enddate']); ?>" />
    </p>
    <?php
}

// Save the meta box data when the post is saved
function brschm_save_event_meta_box($post_id) {
    // Check for nonce security
    if (!isset($_POST['brschm_event_nonce']) || !wp_verify_nonce($_POST['brschm_event_nonce'], 'brschm_save_event_meta_box')) {
        return;
    }

    // Check if it's an autosave (we don't want to save meta during autosave)
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check user permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save event fields
    $fields = [
        'event_status', 'event_category', 'event_country', 'event_city', 'event_address',
        'event_contact', 'event_url', 'event_startdate', 'event_enddate'
    ];

    foreach ($fields as $field) {
        if (isset($_POST['brschm_' . $field])) {
            update_post_meta($post_id, '_brschm_' . $field, sanitize_text_field($_POST['brschm_' . $field]));
        }
    }
}
add_action('save_post', 'brschm_save_event_meta_box');
