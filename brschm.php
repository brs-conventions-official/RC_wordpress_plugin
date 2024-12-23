<?php
/**
 * Plugin Name: BRSCHM
 * Description: The BRSCHM plugin enables Basel, Rotterdam, and Stockholm (BRS) Conventions' Regional Centers on 
 * WordPress to selectively share posts as Documents, News, Events, or Contacts with the CHM Portal at the BRS Secretariat..
 * Version: 1.3
 * Author: BRS Secretariat , Knowledge Management Team , contacts: claire.morel@un.org, vincent@lalieu.com 
 * License: GPLv2 or later
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Include brs-odata functions

require_once plugin_dir_path( __FILE__ ) . 'includes/post-odata-exposer.php';

// Include attachments special field for language
require_once plugin_dir_path( __FILE__ ) . 'includes/attachments.php';

// Include functions for exposing media in odata
//unset //vl241212
require_once plugin_dir_path(__FILE__) . 'includes/media.php';
require_once plugin_dir_path(__FILE__) . 'includes/exposemedia.php';


// Register routes for OData version 4
function odata_register_routes() {

    register_rest_route('odata/v4', '/metadata', [
        'methods' => 'GET',
        'callback' => 'odata_get_metadata_v4',
        'permission_callback' => '__return_true',
    ]);
    register_rest_route('odata/v4', '/posts', [
        'methods' => 'GET',
        'callback' => 'odata_get_posts_v4',
        'permission_callback' => '__return_true',
    ]);
}

add_action('rest_api_init', 'odata_register_routes');

// Include Chm option, topics and chemicals
require_once plugin_dir_path( __FILE__ ) . 'includes/chm-options.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/topics-chemicals.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/events.php';

// Include the contact meta box
include_once plugin_dir_path( __FILE__ ) . 'includes/contacts.php';

// Register the meta box
function brschm_add_meta_box() {
    add_meta_box(
        'brschm_meta_box',
        'BRS Clearing House',
        'brschm_meta_box_html',
        'post',
        'side'
    );
}
add_action( 'add_meta_boxes', 'brschm_add_meta_box' );

// Meta box HTML with CHM options and popups for Topics and Chemicals
function brschm_meta_box_html( $post ) {
    wp_nonce_field( 'brschm_save_meta_box_data', 'brschm_nonce' );

    // Check if the post has a pre-existing tag (document, event, news, or contact)
    $post_tags = wp_get_post_tags( $post->ID, array( 'fields' => 'names' ) );
    $existing_tag = '';

    if (in_array('document', $post_tags)) {
        $existing_tag = 'documents';
    } elseif (in_array('event', $post_tags)) {
        $existing_tag = 'events';
    } elseif (in_array('News', $post_tags)) {
        $existing_tag = 'news';
    } elseif (in_array('Contact', $post_tags)) {
        $existing_tag = 'contacts';
    }

    // Pass the existing tag to the JavaScript
    echo '<input type="hidden" name="chm_option_preselected_tag" value="' . esc_attr($existing_tag) . '" />';

    $logo_url = plugins_url( 'assets/brschm-logo.png', __FILE__ );

    // Display CHM logo button
    echo '<div style="display: flex; align-items: center;">';
    echo '<button type="button" class="button chm-logo-btn" id="chm-logo">';
    echo '<img src="' . esc_url( $logo_url ) . '" alt="CHM Logo" style="width: 24px; height: 24px; margin-right: 10px;" />';
    echo '</button>';
    echo '</div>';

    // Display CHM options (hidden by default)
    echo '<div id="chm-options" style="display:none; margin-top: 10px;">';
    brschm_display_chm_options($post->ID);
    echo '</div>';

    // Call the function to inject Topics and Chemicals modal HTML
    brschm_show_documents_options($post->ID);
}

// Save meta box data and update post tags
function brschm_save_meta_box_data( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! isset( $_POST['brschm_nonce'] ) || ! wp_verify_nonce( $_POST['brschm_nonce'], 'brschm_save_meta_box_data' ) ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    // Save topics
    if ( isset( $_POST['brschm_topics'] ) && is_array( $_POST['brschm_topics'] ) ) {
        update_post_meta( $post_id, '_brschm_topics', array_map( 'sanitize_text_field', $_POST['brschm_topics'] ) );
    } else {
        delete_post_meta( $post_id, '_brschm_topics' );
    }

    // Save chemicals
    if ( isset( $_POST['brschm_chemicals'] ) && is_array( $_POST['brschm_chemicals'] ) ) {
        update_post_meta( $post_id, '_brschm_chemicals', array_map( 'sanitize_text_field', $_POST['brschm_chemicals'] ) );
    } else {
        delete_post_meta( $post_id, '_brschm_chemicals' );
    }

    // Combine topics and chemicals into tags and save them
    $topics = get_post_meta( $post_id, '_brschm_topics', true );
    $chemicals = get_post_meta( $post_id, '_brschm_chemicals', true );
    $tags = array_merge( (array) $topics, (array) $chemicals );

    wp_set_post_tags( $post_id, $tags, true );
}
add_action( 'save_post', 'brschm_save_meta_box_data' );

// Handle AJAX request to save topics and chemicals
function brschm_save_topics_chemicals() {
    check_ajax_referer( 'brschm_chm_nonce', 'nonce' );

    $post_id = intval( $_POST['post_id'] );
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        wp_send_json_error( array( 'message' => 'Permission denied' ) );
    }

    // Retrieve topics and chemicals from the request, default to empty arrays if not set
    $selected_topics = isset( $_POST['topics'] ) ? $_POST['topics'] : [];
    $selected_chemicals = isset( $_POST['chemicals'] ) ? $_POST['chemicals'] : [];

    // Update meta fields for topics and chemicals
    update_post_meta( $post_id, '_brschm_topics', $selected_topics );
    update_post_meta( $post_id, '_brschm_chemicals', $selected_chemicals );

    // Combine selected topics and chemicals to form the new tags list
    $new_tags = array_merge( $selected_topics, $selected_chemicals );

    // Retrieve current tags on the post to preserve non-topic and non-chemical tags
    $current_tags = wp_get_post_tags( $post_id, array( 'fields' => 'names' ) );

    // Preserve existing tags that are not related to topics or chemicals
    $preserved_tags = array_diff( $current_tags, get_post_meta( $post_id, '_brschm_topics', true ) ?: [], get_post_meta( $post_id, '_brschm_chemicals', true ) ?: [] );

    // Merge preserved tags with the new topics and chemicals
    $final_tags = array_merge( $preserved_tags, $new_tags );

    // Update the post tags without appending (overwrite mode)
    wp_set_post_tags( $post_id, $final_tags, false );

    wp_send_json_success();
}


add_action( 'wp_ajax_save_topics_chemicals', 'brschm_save_topics_chemicals' );


// Enqueue custom scripts and styles
function brschm_enqueue_scripts($hook_suffix) {
    if ( 'post.php' == $hook_suffix || 'post-new.php' == $hook_suffix ) {
        wp_enqueue_style( 'brschm-style', plugins_url( 'css/styles.css', __FILE__ ) );
        
        wp_enqueue_script( 'brschm-main-script', plugins_url( 'js/main.js', __FILE__ ), array('jquery'), null, true );
         wp_enqueue_script( 'brschm-events-script', plugins_url( 'js/events.js', __FILE__ ), array('jquery'), null, true );
        wp_enqueue_style('brschm-event-style', plugins_url('css/events.css', __FILE__));
        wp_enqueue_script( 'brschm-contact-script', plugins_url( 'js/contacts.js', __FILE__ ), array('jquery'), null, true );
        wp_enqueue_style('brschm-contact-style', plugins_url('css/contacts.css', __FILE__));


        // **Pass the AJAX URL and nonce to the script**
        wp_localize_script( 'brschm-main-script', 'brschm_ajax', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'brschm_chm_nonce' )  // Correctly pass the nonce
        ));
        wp_localize_script( 'brschm-events-script', 'brschm_ajax', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'brschm_chm_nonce' ),
        ));
    }
}
add_action( 'admin_enqueue_scripts', 'brschm_enqueue_scripts' );




// Handle AJAX request to save topics and chemicals and assign them as tags
function brschm_save_topics_chemicals_ajax() {
    check_ajax_referer( 'brschm_chm_nonce', 'nonce' );

    $post_id = intval( $_POST['post_id'] );
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        wp_send_json_error( array( 'message' => 'Permission denied' ) );
        return;
    }

    // Save Topics
    if ( isset( $_POST['topics'] ) && is_array( $_POST['topics'] ) ) {
        update_post_meta( $post_id, '_brschm_topics', array_map( 'sanitize_text_field', $_POST['topics'] ) );
    } else {
        delete_post_meta( $post_id, '_brschm_topics' );
    }

    // Save Chemicals
    if ( isset( $_POST['chemicals'] ) && is_array( $_POST['chemicals'] ) ) {
        update_post_meta( $post_id, '_brschm_chemicals', array_map( 'sanitize_text_field', $_POST['chemicals'] ) );
    } else {
        delete_post_meta( $post_id, '_brschm_chemicals' );
    }

    // Combine Topics and Chemicals for tags
    $tags = array_merge(
        isset($_POST['topics']) ? $_POST['topics'] : array(),
        isset($_POST['chemicals']) ? $_POST['chemicals'] : array()
    );

    // Assign combined tags to the post
    wp_set_post_tags( $post_id, $tags, true );

    wp_send_json_success( array( 'message' => 'Topics and chemicals saved as tags' ) );
}

// Handle AJAX request to assign the selected CHM tag and remove others
function brschm_assign_chm_tag() {
    // **Verify the nonce**
    if ( ! isset($_POST['nonce']) || ! wp_verify_nonce( $_POST['nonce'], 'brschm_chm_nonce' ) ) {
        wp_send_json_error( array( 'message' => 'Invalid nonce' ), 400 );
    }

    $post_id = intval( $_POST['post_id'] );
    $selected_tag = sanitize_text_field( $_POST['selected_tag'] );

    // Check if the user has permission to edit the post
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        wp_send_json_error( array( 'message' => 'Permission denied' ), 400 );
    }

    // List of mutually exclusive tags
    $exclusive_tags = array('document', 'news', 'event', 'contact');

    // Remove any existing mutually exclusive tags
    wp_remove_object_terms( $post_id, $exclusive_tags, 'post_tag' );

    // Assign the selected tag
    wp_set_post_tags( $post_id, $selected_tag, true );

    wp_send_json_success();
}
add_action( 'wp_ajax_assign_chm_tag', 'brschm_assign_chm_tag' );

// Include the event handler
require_once plugin_dir_path( __FILE__ ) . 'includes/events/events-handler.php';

// Add the event template to the post editor
function add_event_template() {
    global $post;

    // Load the event fields template
    if (file_exists(plugin_dir_path(__FILE__) . 'includes/events/event-template.html')) {
        include(plugin_dir_path(__FILE__) . 'includes/events/event-template.html');
    }

    // Display the fields for event data if needed
    display_event_fields($post);
}
add_action('edit_form_after_title', 'add_event_template');

//*** CHM Tag management
// Handle AJAX request to add the CHM tag
function brschm_add_chm_tag() {
    // Check nonce for security
    check_ajax_referer('brschm_chm_nonce', 'nonce');

    // Get the post ID
    $post_id = intval($_POST['post_id']);

    if (current_user_can('edit_post', $post_id)) {
        // Add the "CHM" tag
        wp_set_post_tags($post_id, 'CHM', true);
        wp_send_json_success();
    } else {
        wp_send_json_error('Permission denied');
    }
}
add_action('wp_ajax_add_chm_tag', 'brschm_add_chm_tag');

//vl240927 Handle AJAX request to remove the CHM tag
function brschm_remove_chm_tag() {
    // Check nonce for security
    check_ajax_referer('brschm_chm_nonce', 'nonce');

    // Get the post ID
    $post_id = intval($_POST['post_id']);

    if (current_user_can('edit_post', $post_id)) {
        // Remove the "CHM" tag
        $current_tags = wp_get_post_tags($post_id, ['fields' => 'names']);
        $new_tags = array_diff($current_tags, ['CHM']);
        wp_set_post_tags($post_id, $new_tags, false);  // Set the new tags, excluding "CHM"
        wp_send_json_success();
    } else {
        wp_send_json_error('Permission denied');
    }
}
add_action('wp_ajax_remove_chm_tag', 'brschm_remove_chm_tag');

//vl240927 Add a hidden field to detect CHM tag on all posts
// Register the meta box to add the hidden field to all posts

function brschm_add_chm_preselection_meta_box() {
    add_meta_box(
        'brschm_chm_meta_box',   // Meta box ID
        'CHM Preselection',      // Visible title (for debugging)
        'brschm_render_chm_preselection',  // Callback function to render the hidden field
        'post',                  // Applies to all post types
        'side',                  // Context (in the sidebar)
        'low'                    // Low priority
    );
}
add_action('add_meta_boxes', 'brschm_add_chm_preselection_meta_box');

// Render the hidden input for CHM preselection
function brschm_render_chm_preselection($post) {
    $chm_preselected_tag = has_tag('CHM', $post->ID) ? 'chm' : '';
    ?>
    <input type="hidden" id="chm-preselected" value="<?php echo esc_attr($chm_preselected_tag); ?>">
    <?php
}

// Register the activation hook
register_activation_hook(__FILE__, 'brschm_plugin_activation');

// Activation function: sets a transient to show the notice on the next admin page load
function brschm_plugin_activation() {
    $to = 'claire.morel@un.org'; 
    $subject = 'BRSCHM Plugin Installed';
    $site_url = get_site_url();
    $message = "The BRSCHM plugin has been activated on the following WordPress site: <a href=\"$site_url\">$site_url</a>";
    $headers = array('Content-Type: text/html; charset=UTF-8');

    // Check if wp_mail exists and attempt to send the email
    if (!function_exists('wp_mail') || !wp_mail($to, $subject, $message, $headers)) {
        // Set a transient to trigger the admin notice if email fails to send
        set_transient('brschm_activation_email_failed', true, 60);
    }
}

// Add action to display the notice based on the transient
add_action('admin_notices', 'brschm_activation_notice');

// Function to display admin notice if the email failed or wp_mail doesn't exist
function brschm_activation_notice() {
    // Check if the transient is set, meaning the email failed to send
    if (get_transient('brschm_activation_email_failed')) {
        ?>
        <div id="message" class="notice notice-error is-dismissible">
            <p>
                <strong>Notice:</strong> 
                The BRSCHM plugin installed successfully! <br>
                Your website is ready to expose data in the ClearingHouse Mechanism Portal. <br>
                Please inform the BRS Secretariat  at <a href="claire.morel@un.org">claire.morel@un.org</a>
                in order to activate the synchronization with the BRS Clearinghouse Mechanism portal. 
            </p>
        </div>
        <?php
        // Delete the transient so the message doesn’t keep showing
        delete_transient('brschm_activation_email_failed');
    }
}
