<?php
// Render the hidden input for CHM preselection
function brschm_render_chm_preselection($post) {
    $chm_preselected_tag = '';

    // Check if the post has the specific tags and set the appropriate value
    if (has_tag('document', $post->ID)) {
        $chm_preselected_tag = 'document';
    } elseif (has_tag('event', $post->ID)) {
        $chm_preselected_tag = 'event';
    } elseif (has_tag('news', $post->ID)) {
        $chm_preselected_tag = 'news';
    } elseif (has_tag('contact', $post->ID)) {
        $chm_preselected_tag = 'contact';
    }

    // Output the hidden input field
    echo '<input type="hidden" id="chm-preselected" value="' . esc_attr($chm_preselected_tag) . '">';
}
