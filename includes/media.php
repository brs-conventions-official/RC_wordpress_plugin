<?php
// Ensure the file is accessed securely.
if (!defined('ABSPATH')) {
    exit;
}

// Include topics and chemicals data.
global $topics, $chemicals;

// Add custom fields to Media Library.
add_filter('attachment_fields_to_edit', function ($form_fields, $post) {
    ob_start();
    ?>
    <div class="brschm_frame">
           <div class="brschm_options">
            <!-- CHM Expose Checkbox -->
            <div class="brschm_twocols">
                <div class="brschm_logo_box ">
                    <img src="<?php echo plugin_dir_url(__FILE__) . '../assets/brschm-logo.png'; ?>" alt="BRSCHM Logo" class="brschm_logo">
                </div>
                <div class="brschm_logo_box ">
                    <input type="checkbox" name="attachments[<?php echo $post->ID; ?>][chm_expose]" value="1" 
                        <?php echo (get_post_meta($post->ID, '_chm_expose', true) ? 'checked="checked"' : ''); ?>>
                    Expose Media to BRS Clearing-House
                </div>
            </div>
            <div class="brschm_twocols">
            <!-- Topics Button -->
            <div class="brschm_button_group">
                <button type="button" class="button" onclick="openMediaModal('brschm_media-topics-modal')">Select Topics</button>
            </div>

            <!-- Chemicals Button -->
            <div class="brschm_button_group">
                <button type="button" class="button" onclick="openMediaModal('brschm_media-chemicals-modal')">Select Chemicals</button>
            </div>
            </div> 
        </div>
    </div>
    <?php
    $form_fields['brschm_frame'] = [
        'label' => '',
        'input' => 'html',
        'html'  => ob_get_clean(),
    ];
    return $form_fields;
}, 10, 2);

// Save custom fields for Media Library.
add_filter('attachment_fields_to_save', function ($post, $attachment) {
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
add_action('admin_enqueue_scripts', function ($hook) {
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
add_action('admin_footer', function () {
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
    <div id="brschm_modal-overlay" class="brschm_modal-overlay"></div>
    <div id="brschm_media-topics-modal" class="brschm_modal" style="display: none;">
        <div class="brschm_modal-content">
            <span class="brschm_close" onclick="closeMediaModal('brschm_media-topics-modal')">&times;</span>
            <h3>Select Topics</h3>
            <div class="brschm_topics-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); grid-gap: 10px;">
                <?php foreach ($topics as $topic): ?>
                    <label>
                        <input type="checkbox" name="media_topics[]" value="<?php echo esc_attr($topic); ?>">
                        <?php echo esc_html($topic); ?>
                    </label>
                <?php endforeach; ?>
            </div>
            <button type="button" class="button brschm_media-save-tags" data-modal-id="brschm_media-topics-modal">Save Topics</button>
        </div>
    </div>

    <!-- Chemicals Modal -->
    <div id="brschm_media-chemicals-modal" class="brschm_modal" style="display: none;">
        <div class="brschm_modal-content">
            <span class="brschm_close" onclick="closeMediaModal('brschm_media-chemicals-modal')">&times;</span>
            <h3>Select Chemicals</h3>
            <div class="brschm_chemicals-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); grid-gap: 10px;">
                <?php foreach ($chemicals as $chemical): ?>
                    <label>
                        <input type="checkbox" name="media_chemicals[]" value="<?php echo esc_attr($chemical); ?>">
                        <?php echo esc_html($chemical); ?>
                    </label>
                <?php endforeach; ?>
            </div>
            <button type="button" class="button brschm_media-save-tags" data-modal-id="brschm_media-chemicals-modal">Save Chemicals</button>
        </div>
    </div>
    <?php
});

// AJAX handler for saving media topics and chemicals.
add_action('wp_ajax_save_media_topics_chemicals', function () {
    check_ajax_referer('brschm_chm_nonce', 'nonce');

    $attachment_id = intval($_POST['attachment_id']);

    if (!current_user_can('edit_post', $attachment_id)) {
        wp_send_json_error(['message' => 'Permission denied']);
    }

    // Retrieve topics and chemicals from the request.
    $topics = isset($_POST['topics']) ? (array) $_POST['topics'] : [];
    $chemicals = isset($_POST['chemicals']) ? (array) $_POST['chemicals'] : [];

    // Save topics and chemicals as meta fields.
    update_post_meta($attachment_id, '_brschm_topics', $topics);
    update_post_meta($attachment_id, '_brschm_chemicals', $chemicals);

    // Combine topics and chemicals into a single array for tagging.
    $tags = array_merge($topics, $chemicals);

    // Assign tags to the media (attachment).
    wp_set_post_tags($attachment_id, $tags, true);

    wp_send_json_success(['message' => 'Tags saved successfully']);
});

// AJAX handler for retrieving media tags.
add_action('wp_ajax_get_media_tags', function () {
    check_ajax_referer('brschm_chm_nonce', 'nonce');

    $attachment_id = intval($_POST['attachment_id']);
    if (!current_user_can('edit_post', $attachment_id)) {
        wp_send_json_error(['message' => 'Permission denied']);
    }

    $topics = get_post_meta($attachment_id, '_brschm_topics', true) ?: [];
    $chemicals = get_post_meta($attachment_id, '_brschm_chemicals', true) ?: [];
    wp_send_json_success(['topics' => $topics, 'chemicals' => $chemicals]);
});
