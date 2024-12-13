<?php
function brschm_expose_media_odata() {
    $args = [
        'post_type'   => 'attachment',
        'post_status' => 'inherit',
        'meta_query'  => [
            [
                'key'     => '_chm_expose',
                'value'   => 1,
                'compare' => '=',
            ],
        ],
        'numberposts' => -1, // Retrieve all attachments 
    ];

    $attachments = get_posts($args);
    $result = [];

    foreach ($attachments as $attachment) {
        $topics = get_post_meta($attachment->ID, '_brschm_topics', true);
        $chemicals = get_post_meta($attachment->ID, '_brschm_chemicals', true);

        $result[] = [
            'ID'        => $attachment->ID,
            'Title'     => $attachment->post_title,
            'Description' => $attachment->post_content, 
            'URL'       => wp_get_attachment_url($attachment->ID),
            'Topics'    => $topics ?: [],
            'Chemicals' => $chemicals ?: [],
            'MimeType'  => $attachment->post_mime_type,
        ];
    }

    return new WP_REST_Response($result, 200);
}


add_action('rest_api_init', function () {
    register_rest_route('odata/v4', '/media', [
        'methods'  => 'GET',
        'callback' => 'brschm_expose_media_odata',
    ]);
});
