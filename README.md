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
**Author**: BRS Secretariat , Knowledge Management Team , contacts: claire.morel@un.org, vincent.lalieu@un.org 
**Version**: 1.0

## Description

The **BRSCHM plugin** is designed for Basel, Rotterdam, and Stockholm (BRS) Conventions' Regional Centers using WordPress. It allows centers to selectively share posts as **Documents**, **News**, **Events**, or **Contacts** with the Central Clearing House Mechanism (CHM) Regional Center Portal at the BRS Secretariat.

### Key Features

- **Selective Content Sharing**: Posts can be categorized as **Documents**, **News**, **Events**, or **Contacts**. This classification allows for relevant information to be easily shared with the CHM Portal.
  
- **OData v4 Integration**: Posts are exposed through **OData v4**, enabling external systems like the CHM Portal to query and display regional center content. Posts can be filtered by tags, custom fields, and attachment types (e.g., PDFs, YouTube videos).

- **Documents**: Posts with official publications, reports, and related materials tagged and categorized with topics and chemicals for easy identification.
  
- **News**: Posts sharing announcements, updates, and developments relevant to the BRS Conventions.
  
- **Events**: Posts promoting conferences, workshops, and other events, including details such as event date, location, and contact information.
  
- **Contacts**: Posts containing key contact information for stakeholders involved in the BRS Conventions.

By integrating the **BRSCHM plugin**, regional centers ensure that their posts are automatically available and queryable by the Central CHM Portal, creating a streamlined communication flow and helping to foster better collaboration across the BRS network.

### Main Features:
- **Tag-Based Classification**: Allows posts to be classified as Documents, News, Events, or Contacts using predefined tags.
- **OData Integration**: Exposes posts via OData v4, making them available to external systems like the CHM Portal.
- **Custom Fields**: Supports custom fields for Events (e.g., event date, location, and contact information), Documents, and other post types.
- **Attachment Handling**: Exposes attached media (PDFs, YouTube videos, images) with filtering options based on MIME type.
- **Flexible Filtering**: Allows querying posts based on various criteria, including tags, custom fields, and attachment types.

## Post Classification and Custom Fields

### Setting a Post as a **Document** and Selecting Topics and Chemicals


![at the bottom left of the screen](https://github.com/brs-conventions-official/RC_wordpress_plugin/blob/main/assets/plugintool.png)

To expose a WordPress post as a **Document** using the BRSCHM plugin:

1. **Select Post Type as Document**:
   - In the post editor, locate the **BRS Clearing House** meta box on the right.
   - Select **Document** as the post type. Once selected, other post types like News, Event, or Contact will automatically be untagged.

2. **Select Topics and Chemicals**:
   - Once marked as a **Document**, a pop-up will appear for selecting **Topics** and **Chemicals**.
     - **Topics**: Choose relevant topics such as "bioaccumulation" or "mercury."
     - **Chemicals**: Select any chemicals mentioned in the document, like "Dieldrin" or "Mirex."
   - These selected Topics and Chemicals will be saved as custom fields (`_brschm_topics`, `_brschm_chemicals`) and exposed in the OData feed.
![Topics selection pad](https://github.com/brs-conventions-official/RC_wordpress_plugin/blob/main/assets/topics_pad.png)
3. **Save the Post**:
   - After selecting the topics and chemicals, when closing the pad , the post is saved and releoaded. It will be exposed as a **Document** in the OData feed with the relevant custom fields.
   - The topics and chemicals selected are visible  in the Post Tags.
   - If tags are not present in the topivs box, you can manually add it in the Tags box of the post

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
## Installation

1. **Backup WordPress Database**  
   Although this module installation is low-risk, it’s good practice to perform a full database backup beforehand using a Backup/Restore plugin (e.g., UpdraftPlus).
2. **Download the Plugin**  
   - In https://github.com/brs-conventions-official/RC_wordpress_plugin , 
   - Click on the **Code** menu and select **Download ZIP** to download the `RC_wordpress_plugin-main.zip` file.

3. **Install the Plugin**  
   - Go to **Add New Plugin** > **Upload Plugin**, select the file, and click **Install Now**.

4. **Activate the Plugin**  
   - In the WordPress admin dashboard, navigate to the **Plugins** section.
   - Find the **BRSCHM** plugin in the list and click **Activate** to enable it.

5. **Send an email to the BRS Secretariat**  
  - a little email to **claire.morel@un.org** , in order to activate the synchronization with BRS portal.


Your BRSCHM plugin should now be installed and active on your WordPress site.

 
## Changelog

### 1.0
- Initial release with OData v4 support for exposing posts, custom fields, and attachments.

---

## License

This plugin is licensed under the GPLv2 or later license. For more details, visit the [GPLv2 license](https://www.gnu.org/licenses/gpl-2.0.html).


[def]: assets/plugintool.png
[def]: assets/topics_pad.png