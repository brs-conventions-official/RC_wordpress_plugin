<?php

error_log('This is a test log entry for the plugin BRSCHM');
// OData v4 metadata
function odata_get_metadata_v4() {
    // Set headers to ensure proper display in browser
    header('Content-Type: application/xml; charset=utf-8');

    // Start XML document and declare the XML version and encoding
    $metadata = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
    $metadata .= '<edmx:Edmx Version="4.0" xmlns:edmx="http://docs.oasis-open.org/odata/ns/edmx" xmlns:m="http://docs.oasis-open.org/odata/ns/edm" >' . "\n";
    $metadata .= '  <edmx:DataServices>' . "\n";
    $metadata .= '    <Schema xmlns="http://docs.oasis-open.org/odata/ns/edm" Namespace="WordPress">' . "\n";

    // Define the Post entity with necessary properties
    $metadata .= '      <EntityType Name="Post">' . "\n";
    $metadata .= '        <Key>' . "\n";
    $metadata .= '          <PropertyRef Name="ID" />' . "\n";
    $metadata .= '        </Key>' . "\n";
    $metadata .= '        <Property Name="ID" Type="Edm.Int32" Nullable="false" />' . "\n";
    $metadata .= '        <Property Name="Title" Type="Edm.String" Nullable="false" />' . "\n";
    $metadata .= '        <Property Name="Content" Type="Edm.String" Nullable="true" />' . "\n";
    $metadata .= '        <Property Name="Tags" Type="Edm.String" Nullable="true" />' . "\n";
    $metadata .= '        <Property Name="Category" Type="Edm.String" Nullable="true" />' . "\n";
    $metadata .= '        <Property Name="Attachments" Type="Collection(WordPress.Attachment)" Nullable="true" />' . "\n";
    $metadata .= '        <Property Name="CustomFields" Type="Collection(WordPress.CustomField)" Nullable="true" />' . "\n";
    $metadata .= '        <Property Name="Modified" Type="Edm.DateTime" Nullable="true" />' . "\n"; // Added Modified property
    $metadata .= '        <Property Name="Weblink" Type="Edm.String" Nullable="true" />' . "\n";
    $metadata .= '        <Property Name="Illustration" Type="WordPress.Attachment" Nullable="true" />' . "\n"; // Illustration image if available
    $metadata .= '      </EntityType>' . "\n";

    // Define the News entity type (same as Post for now, but can be customized)
    $metadata .= '      <EntityType Name="News">' . "\n";
    $metadata .= '        <Key>' . "\n";
    $metadata .= '          <PropertyRef Name="ID" />' . "\n";
    $metadata .= '        </Key>' . "\n";
    $metadata .= '        <Property Name="ID" Type="Edm.Int32" Nullable="false" />' . "\n";
    $metadata .= '        <Property Name="Title" Type="Edm.String" Nullable="false" />' . "\n";
    $metadata .= '        <Property Name="Content" Type="Edm.String" Nullable="true" />' . "\n";
    $metadata .= '        <Property Name="Tags" Type="Edm.String" Nullable="true" />' . "\n";
    $metadata .= '        <Property Name="Category" Type="Edm.String" Nullable="true" />' . "\n";
    $metadata .= '        <Property Name="Attachments" Type="Collection(WordPress.Attachment)" Nullable="true" />' . "\n";
    $metadata .= '        <Property Name="CustomFields" Type="Collection(WordPress.CustomField)" Nullable="true" />' . "\n";
    $metadata .= '        <Property Name="Modified" Type="Edm.DateTime" Nullable="true" />' . "\n"; // Added Modified property
    $metadata .= '        <Property Name="Weblink" Type="Edm.String" Nullable="true" />' . "\n";
    $metadata .= '        <Property Name="Illustration" Type="WordPress.Attachment" Nullable="true" />' . "\n"; // Illustration image if available
    $metadata .= '      </EntityType>' . "\n";

    // Define the Attachment entity type
    $metadata .= '      <EntityType Name="Attachment">' . "\n";
    $metadata .= '        <Key>' . "\n";
    $metadata .= '          <PropertyRef Name="file_url" />' . "\n";
    $metadata .= '        </Key>' . "\n";
    $metadata .= '        <Property Name="file_url" Type="Edm.String" Nullable="false" />' . "\n";
    $metadata .= '        <Property Name="mime_type" Type="Edm.String" Nullable="true" />' . "\n";
    $metadata .= '        <Property Name="language" Type="Edm.String" Nullable="true" />' . "\n"; // Include the language field in attachments
    $metadata .= '      </EntityType>' . "\n";

    // Define the CustomField entity type
    $metadata .= '      <EntityType Name="CustomField">' . "\n";
    $metadata .= '        <Key>' . "\n";
    $metadata .= '          <PropertyRef Name="key" />' . "\n";
    $metadata .= '        </Key>' . "\n";
    $metadata .= '        <Property Name="key" Type="Edm.String" Nullable="false" />' . "\n";
    $metadata .= '        <Property Name="value" Type="Edm.String" Nullable="true" />' . "\n";
    $metadata .= '      </EntityType>' . "\n";

    // Define the entity container with the Posts and News entity sets
    $metadata .= '      <EntityContainer Name="WordPressContext" m:IsDefaultEntityContainer="true">' . "\n";
    $metadata .= '        <EntitySet Name="Posts" EntityType="WordPress.Post" />' . "\n";
    $metadata .= '        <EntitySet Name="News" EntityType="WordPress.News" />' . "\n";
    $metadata .= '        <EntitySet Name="Attachments" EntityType="WordPress.Attachment" />' . "\n";
    $metadata .= '        <EntitySet Name="CustomFields" EntityType="WordPress.CustomField" />' . "\n";
    $metadata .= '      </EntityContainer>' . "\n";

    $metadata .= '    </Schema>' . "\n";
    $metadata .= '  </edmx:DataServices>' . "\n";
    $metadata .= '</edmx:Edmx>';

    // Output the formatted metadata
    echo $metadata;
}


function odata_get_posts_v4() {
    // Get the 'mime_type' parameter from the URL
    $mime_type_param = isset($_GET['mime_type']) ? sanitize_text_field($_GET['mime_type']) : '';

    // Get the 'tags' parameter from the URL
    $tags_param = isset($_GET['tags']) ? sanitize_text_field($_GET['tags']) : '';
    $tags_to_filter = array_filter(explode(',', $tags_param));  // Split and filter out empty values

    // Get the 'ID' parameter from the URL
    $id_param = isset($_GET['ID']) ? intval($_GET['ID']) : '';

    // Get all custom fields passed in the query parameters (excluding known parameters)
    $custom_fields_to_filter = array_filter($_GET, function ($key) {
        return !in_array($key, ['mime_type', 'tags', 'ID']);
    }, ARRAY_FILTER_USE_KEY);

    // Log the received filters for debugging
    error_log("Tags to filter: " . print_r($tags_to_filter, true));
    error_log("MIME type to filter: " . $mime_type_param);
    error_log("ID to filter: " . $id_param);
    error_log("Custom fields to filter: " . print_r($custom_fields_to_filter, true));

    // Build the query arguments for  'post' and 'news' and 'publication' post types
    $args = [
        'post_type' => ['post', 'news','publication'], // Query both post types
        'posts_per_page' => -1, // Retrieve all posts
        'tag' => 'CHM',  // Only posts with the 'CHM' tag are returned
    ];

    // Handle ID filtering
    if (!empty($id_param)) {
        $args['p'] = $id_param; // Filter by post ID
    }

    // Handle tag filtering
    if (!empty($tags_to_filter)) {
        $args['tax_query'] = [
            'relation' => 'AND', // Ensure all tags match
            [
                'taxonomy' => 'post_tag',
                'field' => 'name',
                'terms' => $tags_to_filter,
                'operator' => 'AND', // Require all tags to be present on the post
            ]
        ];
    }

    // Handle custom field filtering
    if (!empty($custom_fields_to_filter)) {
        $args['meta_query'] = [
            'relation' => 'AND'
        ];

        foreach ($custom_fields_to_filter as $meta_key => $meta_value) {
            $args['meta_query'][] = [
                'key' => sanitize_text_field($meta_key),
                'value' => sanitize_text_field($meta_value),
                'compare' => '='
            ];
        }
    }

    // Execute the query with the tag, custom field, and/or ID filters
    $posts = get_posts($args);
    $result = [];

    foreach ($posts as $post) {
        // Initialize $tags to prevent errors
        $tags = wp_get_post_tags($post->ID, ['fields' => 'names']);
        if (!is_array($tags)) {
            $tags = []; // Initialize as empty array if not an array
        }

        // Store the permalink as the weblink
        $weblink = get_permalink($post->ID);

        $attachments_data = [];
        $illustration_url = null; // Reset illustration 
        $has_matching_mime = false; // Track if post has a matching attachment

        // Always retrieve the attached media
        $attachments = get_attached_media('', $post->ID);  // Get all media types

        // Check for YouTube video links in the post content
        $youtube_links = get_youtube_links_from_content($post->post_content);

        // Process regular attachments
        if ($attachments) {
            foreach ($attachments as $attachment) {
                $mime_type = get_post_mime_type($attachment->ID);
                $language = get_post_meta($attachment->ID, 'attachment_language', true); // Retrieve the 'language' field
                $file_url = wp_get_attachment_url($attachment->ID);

                // Check if the file is an image (jpg/png) and assign it as an illustration
                if ($mime_type === 'image/jpeg' || $mime_type === 'image/png') {
                    $extension = ($mime_type === 'image/jpeg') ? 'jpg' : 'png';
                    $illustration_url = $file_url; // Set the illustration URL
                    $illustration_name = 'illustration.' . $extension; // Name the image as illustration
                } elseif (empty($mime_type_param) || $mime_type === $mime_type_param) {
                    // Only include attachments that match the provided MIME type
                    $attachments_data[] = array(
                        'file_url' => $file_url,
                        'mime_type' => $mime_type,
                        'language' => $language // Include the 'language' field in the attachment data
                    );
                    $has_matching_mime = true; // Mark this post as having a matching attachment
                }
            }
        }

        // Add YouTube video links as attachments
        if (!empty($youtube_links)) {
            foreach ($youtube_links as $link) {
                if (empty($mime_type_param) || $mime_type_param === 'video/youtube') {
                    $attachments_data[] = array(
                        'file_url' => $link,
                        'mime_type' => 'video/youtube',
                        'language' => 'EN'
                    );
                    $has_matching_mime = true; // Mark this post as having a matching YouTube video
                }
            }
        }

        // Now let's find any media embedded in the post content
        $embedded_attachments = [];
        $post_content = $post->post_content;

        // Regular expression to find media file URLs in the content
        if (preg_match_all('/<a href=["\']([^"\']+\.(pdf|jpg|jpeg|png|gif|doc|docx))["\']/', $post_content, $matches)) {
            $media_urls = array_unique($matches[1]); // Array of matched media URLs

            foreach ($media_urls as $media_url) {
                // Try to find the attachment by URL and get its meta information
                $attachment_id = attachment_url_to_postid($media_url);
                if ($attachment_id) {
                    $mime_type = get_post_mime_type($attachment_id);
                    $language = get_post_meta($attachment_id, 'attachment_language', true); // Get language field if available

                    $embedded_attachments[] = array(
                        'file_url' => $media_url,
                        'mime_type' => $mime_type,
                        'language' => $language // Include the language field
                    );
                } else {
                    // If the attachment isn't found, we still include the URL without extra metadata
                    $embedded_attachments[] = array(
                        'file_url' => $media_url,
                        'mime_type' => 'unknown',
                        'language' => 'unknown'
                    );
                }
            }
        }

        // Merge both attached and embedded media
        $attachments_data = array_merge($attachments_data, $embedded_attachments);

        // Retrieve categories
        $categories = wp_get_post_categories($post->ID, ['fields' => 'names']);
        $category_list = implode(', ', $categories);

        // Get the latest modification date
        $modified_date = get_post_modified_time('c', false, $post->ID); // ISO 8601 format

        // Retrieve all post meta (custom fields) and filter out empty or null values
        $custom_fields = get_post_meta($post->ID);
        $filtered_custom_fields = [];

        // Only include non-null custom fields
        foreach ($custom_fields as $key => $value) {
            if (!empty($value[0])) {
                if ($key == '_brschm_topics' || $key == '_brschm_chemicals') {
                    $parsed_values = unserialize($value[0]);
                    if (is_array($parsed_values)) {
                        // Format the parsed array as a string separated by single quotes and semicolons
                        $filtered_custom_fields[$key] = implode(";", array_map(function($item) {
                            return "'$item'";
                        }, $parsed_values));
                    } else {
                        $filtered_custom_fields[$key] = $value[0]; // Fallback in case it's not serialized
                    }
                } else {
                    // For non-serialized fields, directly assign the value
                    $filtered_custom_fields[$key] = $value[0];
                }
            }
        }

        // Only include posts that have matching attachments if a MIME type filter is set
        if (empty($mime_type_param) || $has_matching_mime) {
            $result[] = [
                'ID'          => $post->ID,
                'Title'       => $post->post_title,
                'Content'     => $post->post_content,
                'Tags'        => implode(', ', $tags),    // Include tags (now initialized safely)
                'Category'    => $category_list,          // Include categories
                'Attachments' => $attachments_data,       // Include both attached and embedded attachments
                'Modified'    => $modified_date,          // Include modification date
                'CustomFields' => $filtered_custom_fields, // Include custom fields (event metabox, etc.)
                'Weblink'   => $weblink,   //store the url to the original web page
                'Illustration' => ($illustration_url) ? array('file_url' => $illustration_url, 'name' => $illustration_name) : null // Expose illustration image if available
            ];
        }
    }

    return new WP_REST_Response($result, 200);
}




// Function to get YouTube links from post content
function get_youtube_links_from_content($content) {
    $youtube_links = [];

    // Regular expression to find YouTube links
    $pattern = '/https?:\/\/(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/i';

    // Use preg_match_all to find all occurrences of YouTube links in the content
    if (preg_match_all($pattern, $content, $matches)) {
        // Loop through the matches to avoid duplicates
        foreach (array_unique($matches[0]) as $link) {
            $youtube_links[] = $link;
        }
    }

    return $youtube_links;
}





// Add the 'language' field to the REST API response for media attachments
function add_language_to_rest_response($response, $post, $request) {
    $language = get_post_meta($post->ID, 'attachment_language', true);
    if ($language) {
        $response->data['attachment_language'] = $language;
    }

    return $response;
}
add_filter('rest_prepare_attachment', 'add_language_to_rest_response', 10, 3);
