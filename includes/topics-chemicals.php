<?php
// Include topics and chemicals data
include_once plugin_dir_path( __FILE__ ) . 'topics.php';
include_once plugin_dir_path( __FILE__ ) . 'chemicals.php';

// Function to show Topics and Chemicals buttons (Documents section)
function brschm_show_documents_options($post_id) {
    global $topics, $chemicals;

    // Retrieve assigned tags
    $selected_topics = get_post_meta( $post_id, '_brschm_topics', true );
    $selected_chemicals = get_post_meta( $post_id, '_brschm_chemicals', true );

    // Display Topics and Chemicals buttons when "Documents" is clicked
    echo '<div id="documents-options" style="display:none;">'; // Initially hidden, shown on button click
    echo '<button type="button" class="button" id="topics-button" onclick="openModal(\'topics-modal\')">Select Topics</button><br><br>';
    echo '<button type="button" class="button" id="chemicals-button" onclick="openModal(\'chemicals-modal\')">Select Chemicals</button><br><br>';
    echo '</div>';

    // Topics Modal (4 columns layout)
    echo '<div id="topics-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal(\'topics-modal\')">&times;</span>
            <h3>Select Topics</h3>
            <div class="topics-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); grid-gap: 10px;">';

    // Ensure the $topics variable is set and is an array
    if (isset($topics) && is_array($topics)) {
        foreach ($topics as $topic) {
            // Pre-check the already selected topics
            $checked = is_array($selected_topics) && in_array($topic, $selected_topics) ? ' checked' : '';
            echo '<label><input type="checkbox" name="brschm_topics[]" value="' . esc_attr($topic) . '"' . $checked . '> ' . esc_html($topic) . '</label>';
        }
    }
    
    echo '</div></div></div>';

    // Chemicals Modal (3 columns layout)
    echo '<div id="chemicals-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal(\'chemicals-modal\')">&times;</span>
            <h3>Select Chemicals</h3>
            <div class="topics-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); grid-gap: 10px;">';

    // Ensure the $chemicals variable is set and is an array
    if (isset($chemicals) && is_array($chemicals)) {
        foreach ($chemicals as $chemical) {
            // Pre-check the already selected chemicals
            $checked = is_array($selected_chemicals) && in_array($chemical, $selected_chemicals) ? ' checked' : '';
            echo '<label><input type="checkbox" name="brschm_chemicals[]" value="' . esc_attr($chemical) . '"' . $checked . '> ' . esc_html($chemical) . '</label>';
        }
    }

    echo '</div></div></div>';
}

// select topivs and chemicals for the media
/*
function brschm_render_media_modals() {
    include_once plugin_dir_path(__FILE__) . 'topics.php';
    include_once plugin_dir_path(__FILE__) . 'chemicals.php';

    // Topics Modal
    echo '<div id="topics-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal(\'topics-modal\')">&times;</span>
            <h3>Select Topics</h3>
            <div class="topics-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); grid-gap: 10px;">';
    foreach ($topics as $topic) {
        echo '<label><input type="checkbox" name="media_topics[]" value="' . esc_attr($topic) . '"> ' . esc_html($topic) . '</label>';
    }
    echo '</div></div></div>';

    // Chemicals Modal
    echo '<div id="chemicals-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal(\'chemicals-modal\')">&times;</span>
            <h3>Select Chemicals</h3>
            <div class="topics-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); grid-gap: 10px;">';
    foreach ($chemicals as $chemical) {
        echo '<label><input type="checkbox" name="media_chemicals[]" value="' . esc_attr($chemical) . '"> ' . esc_html($chemical) . '</label>';
    }
    echo '</div></div></div>';
}
add_action('admin_footer', 'brschm_render_media_modals');
*/

