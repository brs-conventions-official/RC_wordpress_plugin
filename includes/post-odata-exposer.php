<?php


// OData v1 metadata
function odata_get_metadata_v1() {
    $metadata = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
    $metadata .= '<edmx:Edmx Version="1.0" xmlns:edmx="http://schemas.microsoft.com/ado/2007/06/edmx">' . "\n";
    $metadata .= '  <edmx:DataServices>' . "\n";
    $metadata .= '    <Schema xmlns="http://schemas.microsoft.com/ado/2006/04/edm" Namespace="WordPressOData">' . "\n";
    $metadata .= '      <EntityType Name="Post">' . "\n";
    $metadata .= '        <Key>' . "\n";
    $metadata .= '          <PropertyRef Name="ID" />' . "\n";
    $metadata .= '        </Key>' . "\n";
    $metadata .= '        <Property Name="ID" Type="Edm.Int32" Nullable="false" />' . "\n";
    $metadata .= '        <Property Name="Title" Type="Edm.String" Nullable="false" />' . "\n";
    $metadata .= '        <Property Name="Content" Type="Edm.String" Nullable="true" />' . "\n";
    $metadata .= '        <Property Name="Tags" Type="Edm.String" Nullable="true" />' . "\n";
    $metadata .= '        <Property Name="Category" Type="Edm.String" Nullable="true" />' . "\n";
    $metadata .= '        <Property Name="Attachments" Type="Edm.String" Nullable="true" />' . "\n";
    $metadata .= '        <Property Name="Modified" Type="Edm.DateTime" Nullable="true" />' . "\n"; // Added Modified property
    $metadata .= '      </EntityType>' . "\n";
    $metadata .= '      <EntityContainer Name="WordPressODataContext" m:IsDefaultEntityContainer="true">' . "\n";
    $metadata .= '        <EntitySet Name="Posts" EntityType="WordPressOData.Post" />' . "\n";
    $metadata .= '      </EntityContainer>' . "\n";
    $metadata .= '    </Schema>' . "\n";
    $metadata .= '  </edmx:DataServices>' . "\n";
    $metadata .= '</edmx:Edmx>';

    return new WP_REST_Response($metadata, 200);
}

// OData v1 posts with modification date, categories, and attachments always included
function odata_get_posts_v1($data) {
    $filter = isset($data['$filter']) ? $data['$filter'] : '';

    $args = array(
        'post_type'   => 'post',
        'numberposts' => 16,  // Adjust as needed
    );

    // MIME type filter handling
    $mime_type_filter = null;
    if (preg_match('/mimeType eq \'([^\']+)\'/', $filter, $matches)) {
        $mime_type_filter = $matches[1];
    }

    $posts = get_posts($args);
    $output = [];

    foreach ($posts as $post) {
        // Initialize attachments data
        $attachments_data = [];
        $tags = wp_get_post_tags($post->ID, ['fields' => 'names']);

        // Always retrieve the attachments
        $attachments = get_attached_media('', $post->ID);  // Get all media types

        foreach ($attachments as $attachment) {
            $mime_type = get_post_mime_type($attachment->ID);
            if ($mime_type_filter === null || $mime_type === $mime_type_filter) {
                $attachments_data[] = array(
                    'file_url' => wp_get_attachment_url($attachment->ID),
                    'mime_type' => $mime_type
                );
            }
        }

        // Retrieve categories
        $categories = wp_get_post_categories($post->ID, ['fields' => 'names']);
        $category_list = implode(', ', $categories);

        // Get the latest modification date
        $modified_date = get_post_modified_time('c', false, $post->ID); // ISO 8601 format

        // Retrieve all post meta (custom fields, including event fields)
        $custom_fields = get_post_meta($post->ID);

        // Skip posts with no matching attachments if filter is set
        if (empty($attachments_data) && $mime_type_filter) {
            continue;
        }

        $output[] = array(
            'id'          => $post->ID,
            'title'       => $post->post_title,
            'content'     => $post->post_content,
            'tags'        => implode(', ', $tags),    // Include tags
            'category'    => $category_list,          // Include categories
            'attachments' => $attachments_data,       // Always include the attachments
            'modified'    => $modified_date,          // Include modification date
            'custom_fields' => $custom_fields,        // Include custom fields (event metabox, etc.)
        );
    }

    return new WP_REST_Response($output, 200);
}

// OData v4 metadata
function odata_get_metadata_v4() {
    $metadata = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
    $metadata .= '<edmx:Edmx Version="4.0" xmlns:edmx="http://docs.oasis-open.org/odata/ns/edmx">' . "\n";
    $metadata .= '  <edmx:DataServices>' . "\n";
    $metadata .= '    <Schema xmlns="http://docs.oasis-open.org/odata/ns/edm" Namespace="WordPress">' . "\n";
    $metadata .= '      <EntityType Name="Post">' . "\n";
    $metadata .= '        <Key>' . "\n";
    $metadata .= '          <PropertyRef Name="ID" />' . "\n";
    $metadata .= '        </Key>' . "\n";
    $metadata .= '        <Property Name="ID" Type="Edm.Int32" Nullable="false" />' . "\n";
    $metadata .= '        <Property Name="Title" Type="Edm.String" Nullable="false" />' . "\n";
    $metadata .= '        <Property Name="Content" Type="Edm.String" Nullable="true" />' . "\n";
    $metadata .= '        <Property Name="Tags" Type="Edm.String" Nullable="true" />' . "\n";
    $metadata .= '        <Property Name="Category" Type="Edm.String" Nullable="true" />' . "\n";
    $metadata .= '        <Property Name="Attachments" Type="Edm.String" Nullable="true" />' . "\n";
    $metadata .= '        <Property Name="Modified" Type="Edm.DateTime" Nullable="true" />' . "\n"; // Added Modified property
    $metadata .= '      </EntityType>' . "\n";
    $metadata .= '      <EntityContainer Name="WordPressContext" m:IsDefaultEntityContainer="true">' . "\n";
    $metadata .= '        <EntitySet Name="Posts" EntityType="WordPress.Post" />' . "\n";
    $metadata .= '      </EntityContainer>' . "\n";
    $metadata .= '    </Schema>' . "\n";
    $metadata .= '  </edmx:DataServices>' . "\n";
    $metadata .= '</edmx:Edmx>';

    return new WP_REST_Response($metadata, 200);
}

// OData v4 posts with modification date, categories, and attachments always included
function odata_get_posts_v4() {
    $tag = isset($_GET['tag']) ? sanitize_text_field($_GET['tag']) : '';

    $args = [
        'post_type' => 'post',
        'posts_per_page' => -1, // Retrieve all posts
    ];

    if ($tag) {
        $args['tag'] = $tag; // Filter by tag
    }

    $posts = get_posts($args);
    $result = [];

    foreach ($posts as $post) {
        $tags = wp_get_post_tags($post->ID, ['fields' => 'names']);
        $attachments_data = [];

        // Always retrieve the attachments
        $attachments = get_attached_media('', $post->ID);  // Get all media types

        if ($attachments) {
            foreach ($attachments as $attachment) {
                $attachments_data[] = array(
                    'file_url' => wp_get_attachment_url($attachment->ID),
                    'mime_type' => get_post_mime_type($attachment->ID)
                );
            }
        }

        // Retrieve categories
        $categories = wp_get_post_categories($post->ID, ['fields' => 'names']);
        $category_list = implode(', ', $categories);

        // Get the latest modification date
        $modified_date = get_post_modified_time('c', false, $post->ID); // ISO 8601 format
        
        // Retrieve all post meta (custom fields, including event fields)
        $custom_fields = get_post_meta($post->ID);


        $result[] = [
            'ID'          => $post->ID,
            'Title'       => $post->post_title,
            'Content'     => $post->post_content,
            'Tags'        => implode(', ', $tags),    // Include tags
            'Category'    => $category_list,          // Include categories
            'Attachments' => $attachments_data,       // Always include the attachments
            'Modified'    => $modified_date,          // Include modification date
            'CustomFields' => $custom_fields,         // Include custom fields (event metabox, etc.)
        ];
    }
    return new WP_REST_Response($result, 200);
}
