<?php
// Ensure the file is accessed securely.
if (!defined('ABSPATH')) {
    exit;
}


// Include topics and chemicals data.
global $topics, $chemicals;

// Add custom fields to Media Library.
add_filter('attachment_fields_to_edit', function($form_fields, $post) {
    // Add CHM options
    $form_fields['brschm_topics_button'] = [
        'label' => 'Topics',
        'input' => 'html',
        'html'  => '<button type="button" class="button" onclick="openMediaModal(\'media-topics-modal\')">Select Topics</button>',
    ];
    $form_fields['brschm_chemicals_button'] = [
        'label' => 'Chemicals',
        'input' => 'html',
        'html'  => '<button type="button" class="button" onclick="openMediaModal(\'media-chemicals-modal\')">Select Chemicals</button>',
    ];

    // Add CHM Expose checkbox
    $form_fields['chm_expose'] = [
        'label' => 'Expose as CHM Document',
        'input' => 'html',
        'html'  => '<input type="checkbox" name="attachments[' . $post->ID . '][chm_expose]" value="1" ' . (get_post_meta($post->ID, '_chm_expose', true) ? 'checked="checked"' : '') . '> Expose to CHM',
    ];

    return $form_fields;
}, 10, 2);

// Save custom fields for Media Library.
add_filter('attachment_fields_to_save', function($post, $attachment) {
    if (isset($attachment['brschm_topics'])) {
        update_post_meta($post['ID'], '_brschm_topics', $attachment['brschm_topics']);
    }
    if (isset($attachment['brschm_chemicals'])) {
        update_post_meta($post['ID'], '_brschm_chemicals', $attachment['brschm_chemicals']);
    }
    if (isset($attachment['chm_expose'])) {
        update_post_meta($post['ID'], '_chm_expose', 1);
    } else {
        delete_post_meta($post['ID'], '_chm_expose');
    }
    return $post;
}, 10, 2);

// Enqueue admin scripts and styles for modals.
add_action('admin_enqueue_scripts', function($hook) {
    if ($hook === 'upload.php') { // Only load on Media Library page.
        wp_enqueue_style('brschm-admin-styles', plugin_dir_url(__FILE__) . 'css/media.css');
        wp_enqueue_script('brschm-admin-scripts', plugin_dir_url(__FILE__) . 'js/media.js', ['jquery'], null, true);
        wp_localize_script('brschm-admin-scripts', 'brschm_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('brschm_chm_nonce'),
        ]);
    }
});

// Output modals in the footer of the Media Library.
add_action('admin_footer', function() {
    global $topics, $chemicals;

    // Ensure topics and chemicals are loaded.
    if (!isset($topics)) {
        include_once plugin_dir_path(__FILE__) . 'topics.php';
    }
    if (!isset($chemicals)) {
        include_once plugin_dir_path(__FILE__) . 'chemicals.php';
    }

    ?>

    <!-- Topics Modal -->
    <div id="modal-overlay" class="modal-overlay"></div>
    <div id="media-topics-modal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close" onclick="closeMediaModal('media-topics-modal')">&times;</span>
            <h3>Select Topics</h3>
            <div class="topics-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); grid-gap: 10px;">
                <?php foreach ($topics as $topic): ?>
                    <label>
                        <input type="checkbox" name="media_topics[]" value="<?php echo esc_attr($topic); ?>">
                        <?php echo esc_html($topic); ?>
                    </label>
                <?php endforeach; ?>
            </div>
            <button type="button" class="button media-save-tags" data-modal-id="media-topics-modal">Save Topics</button>
        </div>
    </div>

    <!-- Chemicals Modal -->
    <div id="media-chemicals-modal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close" onclick="closeMediaModal('media-chemicals-modal')">&times;</span>
            <h3>Select Chemicals</h3>
            <div class="chemicals-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); grid-gap: 10px;">
                <?php foreach ($chemicals as $chemical): ?>
                    <label>
                        <input type="checkbox" name="media_chemicals[]" value="<?php echo esc_attr($chemical); ?>">
                        <?php echo esc_html($chemical); ?>
                    </label>
                <?php endforeach; ?>
            </div>
            <button type="button" class="button media-save-tags" data-modal-id="media-chemicals-modal">Save Chemicals</button>
        </div>
    </div>
    <?php
});

// AJAX handler for saving media topics and chemicals.
add_action('wp_ajax_save_media_topics_chemicals', function() {
    check_ajax_referer('brschm_chm_nonce', 'nonce');

    $attachment_id = intval($_POST['attachment_id']);

    if (!current_user_can('edit_post', $attachment_id)) {
        wp_send_json_error(['message' => 'Permission denied']);
    }

    // Retrieve topics and chemicals from the request
    $topics = isset($_POST['topics']) ? (array) $_POST['topics'] : [];
    $chemicals = isset($_POST['chemicals']) ? (array) $_POST['chemicals'] : [];

    // Save topics and chemicals as meta fields
    update_post_meta($attachment_id, '_brschm_topics', $topics);
    update_post_meta($attachment_id, '_brschm_chemicals', $chemicals);

    // Combine topics and chemicals into a single array for tagging
    $tags = array_merge($topics, $chemicals);

    // Assign tags to the media (attachment)
    wp_set_post_tags($attachment_id, $tags, true);

    wp_send_json_success(['message' => 'Tags saved successfully']);
});


function brschm_get_media_tags() {
    check_ajax_referer('brschm_chm_nonce', 'nonce');

    $attachment_id = intval($_POST['attachment_id']);
    if (!current_user_can('edit_post', $attachment_id)) {
        wp_send_json_error(['message' => 'Permission denied']);
    }

    $topics = get_post_meta($attachment_id, '_brschm_topics', true) ?: [];
    $chemicals = get_post_meta($attachment_id, '_brschm_chemicals', true) ?: [];
    wp_send_json_success(['topics' => $topics, 'chemicals' => $chemicals]);
}
add_action('wp_ajax_get_media_tags', 'brschm_get_media_tags');




