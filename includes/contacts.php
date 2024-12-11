<?php
// Register the meta box for Contacts
function brschm_register_contact_meta_box() {
    if (isset($post->ID) && get_post_type($post->ID) === 'post' && has_tag('contact', $post->ID))  {
                add_meta_box(
                    'brschm_contact_meta_box', // Meta box ID
                    'Contact Details',         // Title of the meta box
                    'brschm_render_contact_meta_box',  // Callback function to display the content
                    'post',                    // Post type
                    'normal',                  // Context (normal, side, etc.)
                    'high'                     // Priority
                );
            }
}
add_action('add_meta_boxes', 'brschm_register_contact_meta_box');

// Render the meta box content
function brschm_render_contact_meta_box($post) {
    // Add a nonce field for security
    wp_nonce_field('brschm_save_contact_meta_box', 'brschm_contact_nonce');

    // Get existing contact field values (if any)
    $fields = [
        'contact_prefix' => get_post_meta($post->ID, '_brschm_contact_prefix', true),
        'contact_firstname' => get_post_meta($post->ID, '_brschm_contact_firstname', true),
        'contact_lastname' => get_post_meta($post->ID, '_brschm_contact_lastname', true),
        'contact_address' => get_post_meta($post->ID, '_brschm_contact_address', true),
        'contact_city' => get_post_meta($post->ID, '_brschm_contact_city', true),
        'contact_country' => get_post_meta($post->ID, '_brschm_contact_country', true),
        'contact_organisation' => get_post_meta($post->ID, '_brschm_contact_organisation', true),
        'contact_departement' => get_post_meta($post->ID, '_brschm_contact_departement', true),
        'contact_position' => get_post_meta($post->ID, '_brschm_contact_position', true),
        'contact_email' => get_post_meta($post->ID, '_brschm_contact_email', true),
        'contact_phone' => get_post_meta($post->ID, '_brschm_contact_phone', true),
        'contact_type' => get_post_meta($post->ID, '_brschm_contact_type', true)
    ];

    ?>
    <p>
        <label for="brschm_contact_prefix">Prefix:</label>
        <input type="text" id="brschm_contact_prefix" name="brschm_contact_prefix" value="<?php echo esc_attr($fields['contact_prefix']); ?>" placeholder="Enter contact prefix" />
    </p>
    <p>
        <label for="brschm_contact_firstname">First Name:</label>
        <input type="text" id="brschm_contact_firstname" name="brschm_contact_firstname" value="<?php echo esc_attr($fields['contact_firstname']); ?>" placeholder="Enter first name" />
    </p>
    <p>
        <label for="brschm_contact_lastname">Last Name:</label>
        <input type="text" id="brschm_contact_lastname" name="brschm_contact_lastname" value="<?php echo esc_attr($fields['contact_lastname']); ?>" placeholder="Enter last name" />
    </p>
    <p>
        <label for="brschm_contact_address">Address:</label>
        <input type="text" id="brschm_contact_address" name="brschm_contact_address" value="<?php echo esc_attr($fields['contact_address']); ?>" placeholder="Enter contact address" />
    </p>
    <p>
        <label for="brschm_contact_city">City:</label>
        <input type="text" id="brschm_contact_city" name="brschm_contact_city" value="<?php echo esc_attr($fields['contact_city']); ?>" placeholder="Enter contact city" />
    </p>
    <p>
        <label for="brschm_contact_country">Country:</label>
        <input type="text" id="brschm_contact_country" name="brschm_contact_country" value="<?php echo esc_attr($fields['contact_country']); ?>" placeholder="Enter contact country" />
    </p>
    <p>
        <label for="brschm_contact_organisation">Organisation:</label>
        <input type="text" id="brschm_contact_organisation" name="brschm_contact_organisation" value="<?php echo esc_attr($fields['contact_organisation']); ?>" placeholder="Enter organisation" />
    </p>
    <p>
        <label for="brschm_contact_departement">Departement:</label>
        <input type="text" id="brschm_contact_departement" name="brschm_contact_departement" value="<?php echo esc_attr($fields['contact_departement']); ?>" placeholder="Enter department" />
    </p>
    <p>
        <label for="brschm_contact_position">Position:</label>
        <input type="text" id="brschm_contact_position" name="brschm_contact_position" value="<?php echo esc_attr($fields['contact_position']); ?>" placeholder="Enter position" />
    </p>
    <p>
        <label for="brschm_contact_email">Email:</label>
        <input type="email" id="brschm_contact_email" name="brschm_contact_email" value="<?php echo esc_attr($fields['contact_email']); ?>" placeholder="Enter email address" />
    </p>
    <p>
        <label for="brschm_contact_phone">Phone Number:</label>
        <input type="text" id="brschm_contact_phone" name="brschm_contact_phone" value="<?php echo esc_attr($fields['contact_phone']); ?>" placeholder="Enter phone number" />
    </p>
    <p>
        <label for="brschm_contact_type">Type:</label>
        <input type="text" id="brschm_contact_type" name="brschm_contact_type" value="<?php echo esc_attr($fields['contact_type']); ?>" placeholder="Enter contact type" />
    </p>
    <?php
}

// Save the meta box data when the post is saved
function brschm_save_contact_meta_box($post_id) {
    // Check for nonce security
    if (!isset($_POST['brschm_contact_nonce']) || !wp_verify_nonce($_POST['brschm_contact_nonce'], 'brschm_save_contact_meta_box')) {
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

    // Save contact fields
    $fields = [
        'contact_prefix', 'contact_firstname', 'contact_lastname', 'contact_address', 'contact_city',
        'contact_country', 'contact_organisation', 'contact_departement', 'contact_position',
        'contact_email', 'contact_phone', 'contact_type'
    ];

    foreach ($fields as $field) {
        if (isset($_POST['brschm_' . $field])) {
            update_post_meta($post_id, '_brschm_' . $field, sanitize_text_field($_POST['brschm_' . $field]));
        }
    }
}
add_action('save_post', 'brschm_save_contact_meta_box');
