# BRSCHM Plugin for Basel, Rotterdam, and Stockholm Conventions

**Contributors**: BRS Secretariat  
**Tags**: CHM, OData, Events, Documents, News, Contacts  
**Requires at least**: 5.6  
**Tested up to**: 6.0  
**Stable tag**: 1.0  
**Requires PHP**: 7.4  
**License**: GPLv2 or later  
**License URI**: https://www.gnu.org/licenses/gpl-2.0.html  
**GitHub Repository**: https://github.com/brs-conventions-official/RC_wordpress_plugin  
**Plugin URI**: https://brschm.astarte.io/  
**Author**: BRS Secretariat IT Team , vincent.lalieu@un.org 
**Version**: 1.0

## Description

The **BRSCHM plugin** allows BRS Regional Centers using WordPress to select their posts for exposure as **Documents**, **News**, **Events**, or **Contacts** to the Central Clearing House Mechanism (CHM) Regional Center Portal at the BRS Secretariat.

This plugin also allows the BRS Regional Centres to expose specific WordPress posts via OData v4 as **Documents**, **News**, **Events**, or **Contacts** to share with the Central Clearing House Mechanism (CHM) Regional Center Portal at the BRS Secretariat. Posts can be tagged accordingly and shared through OData for integration with the CHM system.

### Main Features:
- **Tag-Based Classification**: Allows posts to be classified as Documents, News, Events, or Contacts using predefined tags.
- **OData Integration**: Exposes posts via OData v4, making them available to external systems like the CHM Portal.
- **Custom Fields**: Supports custom fields for Events (e.g., event date, location, and contact information), Documents, and other post types.
- **Attachment Handling**: Exposes attached media (PDFs, YouTube videos, images) with filtering options based on MIME type.
- **Flexible Filtering**: Allows querying posts based on various criteria, including tags, custom fields, and attachment types.

## Post Classification and Custom Fields

### Setting a Post as a **Document** and Selecting Topics and Chemicals

To expose a WordPress post as a **Document** using the BRSCHM plugin:

1. **Select Post Type as Document**:
   - In the post editor, locate the **BRS Clearing House** meta box on the right.
   - Select **Document** as the post type. Once selected, other post types like News, Event, or Contact will automatically be untagged.

2. **Select Topics and Chemicals**:
   - Once marked as a **Document**, a pop-up will appear for selecting **Topics** and **Chemicals**.
     - **Topics**: Choose relevant topics such as "bioaccumulation" or "mercury."
     - **Chemicals**: Select any chemicals mentioned in the document, like "Dieldrin" or "Mirex."
   - These selected Topics and Chemicals will be saved as custom fields (`_brschm_topics`, `_brschm_chemicals`) and exposed in the OData feed.

3. **Save the Post**:
   - After selecting the topics and chemicals, save the post. It will be exposed as a **Document** in the OData feed with the relevant custom fields.

---

### Setting a Post as **News**

To expose a post as **News**:

1. **Select Post Type as News**:
   - In the **BRS Clearing House** meta box, select **News** as the post type. Other post types will be untagged automatically.

2. **No Additional Fields**:
   - No extra custom fields are required for **News** posts.

3. **Save the Post**:
   - Save the post once you select **News**. The post will be exposed in the OData feed as **News**.

---

### Setting a Post as an **Event** and Entering Event Fields

For **Event** posts, the following steps are necessary:

1. **Select Post Type as Event**:
   - In the **BRS Clearing House** meta box, select **Event** as the post type. Other post types will be untagged.

2. **Enter Event Fields**:
   - After selecting **Event**, you'll see additional fields to complete:
     - **Event Name** (`_brschm_event_name`): The event's name.
     - **Event Date** (`_brschm_event_date`): The event's date.
     - **Event Location** (`_brschm_event_location`): Where the event is taking place.
     - **Event Country** (`_brschm_event_country`): The country of the event.
     - **Event City** (`_brschm_event_city`): The city of the event.
     - **Event Contact** (`_brschm_event_contact`): The contact person or organization.
     - **Event Start Date** (`_brschm_event_startdate`): The event's start date.
     - **Event End Date** (`_brschm_event_enddate`): The event's end date (optional).

3. **Save the Post**:
   - After completing the fields, save the post. It will be exposed in the OData feed as an **Event** with the corresponding metadata.

---

### Setting a Post as **Contact** and Entering Contact Fields

To expose a post as a **Contact**:

1. **Select Post Type as Contact**:
   - In the **BRS Clearing House** meta box, select **Contact**. Other post types will be untagged.

2. **Enter Contact Fields**:
   - Fill out the **Contact**-specific fields:
     - **Contact Name** (`_brschm_contact_name`): The name of the contact.
     - **Contact Organization** (`_brschm_contact_organization`): The associated organization.
     - **Contact Email** (`_brschm_contact_email`): The contact's email.

3. **Save the Post**:
   - Once you've filled in the contact information, save the post. It will be exposed in the OData feed as a **Contact** post.

---

## OData Queries and Examples

Here are some example queries to filter posts based on various criteria:

- **Filter Posts by Event Country**:
    https://yourdomain.com/wp-json/odata/v4/posts?_brschm_event_country=Austria

- **Filter Documents by Topics**:
    https://yourdomain.com/wp-json/odata/v4/posts?_brschm_topics=bioaccumulation,mercury

- **Filter Contact Posts by Contact Name**:
    https://yourdomain.com/wp-json/odata/v4/posts?_brschm_contact_name=Center%20of%20Health%20and%20Food,%20Austria

- **Filter Posts by YouTube Video Attachments**:
    https://yourdomain.com/wp-json/odata/v4/posts?mime_type=video/youtube

---

### Examples of OData Queries

The following are examples of OData queries that can be used to filter and retrieve posts exposed by the plugin.

### 1. **Retrieve All Posts**
https://yourdomain.com/wp-json/odata/v4/posts

### 2. **Filter Posts by Tags (e.g., CHM and Document)**
Retrieve posts tagged as both "CHM" and "document":
https://yourdomain.com/wp-json/odata/v4/posts?tags=CHM,document

### 3. **Filter Posts with PDF Attachments**
Retrieve posts that contain PDF attachments:
https://yourdomain.com/wp-json/odata/v4/posts?mime_type=application/pdf

### 4. **Filter Posts with YouTube Video Attachments**
Retrieve posts that contain YouTube video links:
https://yourdomain.com/wp-json/odata/v4/posts?mime_type=video/youtube

### 5. **Filter by Event Country (e.g., Austria)**
Retrieve posts where the custom field `_brschm_event_country` is "Austria":
https://yourdomain.com/wp-json/odata/v4/posts?_brschm_event_country=Austria

### 8. **Retrieve Posts with a Specific ID**
Retrieve a specific post by its ID (e.g., post ID = 127):
https://yourdomain.com/wp-json/odata/v4/posts?ID=127

### 9. **Filter Posts by Attachment Type (e.g., YouTube and PDF)**
Retrieve posts that contain both YouTube videos and PDF attachments:
https://yourdomain.com/wp-json/odata/v4/posts?mime_type=application/pdf,video/youtube

---

## Custom Fields for Events, Documents, and Contacts

This plugin allows Regional Centers to add custom metadata for posts that are categorized as Events, Documents, or Contacts. These custom fields allow for detailed metadata, which can be exposed through OData. Only non-empty custom fields are exposed, keeping the data relevant.

### Event Custom Fields:
- `_brschm_event_name`: Name of the event.
- `_brschm_event_date`: Date of the event.
- `_brschm_event_location`: Event location.
- `_brschm_event_country`: Country where the event takes place.
- `_brschm_event_city`: City where the event takes place.
- `_brschm_event_contact`: Contact person or organization for the event.

### Document Custom Fields:
- `_brschm_topics`: Topics related to the document.
- `_brschm_chemicals`: Chemicals discussed in the document.
- `_brschm_document_category`: Document category.

### Contact Custom Fields:
- `_brschm_contact_name`: Name of the contact.
- `_brschm_contact_organization`: Organization of the contact.
- `_brschm_contact_email`: Email address of the contact.

---

## Metadata Exposed

The following metadata is exposed by the plugin for each post:
- **Post ID**: Unique identifier of the post.
- **Title**: Title of the post.
- **Content**: Main content of the post.
- **Tags**: List of tags assigned to the post.
- **Category**: Category of the post.
- **Attachments**: All attached files (PDFs, images, YouTube videos).
- **Custom Fields**: Relevant custom fields (only non-empty values are exposed).
- **Illustration**: Primary image attachment for the post, if available.

---

## Changelog

### 1.0
- Initial release with OData v4 support for exposing posts, custom fields, and attachments.

---

## License

This plugin is licensed under the GPLv2 or later license. For more details, visit the [GPLv2 license](https://www.gnu.org/licenses/gpl-2.0.html).
