# BRSCHM

**Contributors**: BRS Secretariat  
**Tags**: CHM, OData, Events, Documents, News, Contacts, Media  
**Requires at least**: 5.6  
**Tested up to**: 6.6  
**Stable tag**: 1.2 
**Requires PHP**: 7.4  
**License**: GPLv2 or later  
**License URI**: https://www.gnu.org/licenses/gpl-2.0.html  
**GitHub Repository**: https://github.com/brs-conventions-official/RC_wordpress_plugin  
**Plugin URI**: https://brschm.astarte.io/  
**Author**: BRS Secretariat, Knowledge Management Team  
**Contacts**: claire.morel@un.org, vincent@lalieu.com  
**Version**: 1.2

## Description

The **BRSCHM plugin** is designed for Basel, Rotterdam, and Stockholm (BRS) Conventions' Regional Centers using WordPress. It allows centers to selectively share posts and media as **Documents**, **News**, **Events**, or **Contacts** with the Central Clearing House Mechanism (CHM) Regional Center Portal at the BRS Secretariat.  
**Short introduction Video:** https://www.youtube.com/watch?v=OcJu-RagdkA

### Key Features

- **Selective Content Sharing**: Posts and media can be categorized as **Documents**, **News**, **Events**, or **Contacts**. This classification allows for relevant information to be easily shared with the CHM Portal.

- **OData v4 Integration**: Posts and media are exposed through **OData v4**, enabling external systems like the CHM Portal to query and display regional center content. Media such as PDFs, images, and videos can also be tagged and shared.

- **Documents**: Posts with official publications, reports, and related materials tagged and categorized with topics and chemicals for easy identification.

- **Media Exposure**: Attachments like PDFs, images, and videos can now be exposed directly without associating them with posts. Topics and chemicals can be assigned to media, enabling seamless integration into the CHM Portal.

- **News**: Posts sharing announcements, updates, and developments relevant to the BRS Conventions.

- **Events**: Posts promoting conferences, workshops, and other events, including details such as event date, location, and contact information.

- **Contacts**: Posts containing key contact information for stakeholders involved in the BRS Conventions.

By integrating the **BRSCHM plugin**, regional centers ensure that their posts and media are automatically available and queryable by the Central CHM Portal, creating a streamlined communication flow and helping to foster better collaboration across the BRS network.

### Main Features:

- **Tag-Based Classification**: Allows posts and media to be classified as Documents, News, Events, or Contacts using predefined tags.
- **OData Integration**: Exposes posts and media via OData v4, making them available to external systems like the CHM Portal.
- **Custom Fields**: Supports custom fields for Events (e.g., event date, location, and contact information), Documents, and other post types.
- **Media Tagging**: Topics and chemicals can be assigned directly to media files (PDFs, videos, images) and exposed via OData.
- **Attachment Handling**: Exposes attached media (PDFs, YouTube videos, images) with filtering options based on MIME type.
- **Flexible Filtering**: Allows querying posts and media based on various criteria, including tags, custom fields, and attachment types.

---

## Selecting Media for OData Exposure

The plugin allows Regional Centers to expose media such as PDFs, images, and videos directly to the CHM Portal.

### Steps to Expose Media:

1. **Enable CHM Exposure**:
   - In the WordPress Media Library, select a media item.
   - Check the "Expose as CHM Document" box to enable OData exposure for the selected media.

2. **Assign Topics and Chemicals**:
   - Click on "Select Topics" or "Select Chemicals" buttons to open the modal interface.
   - Choose relevant topics and chemicals from the modal. Selected values will be saved and associated with the media.

3. **Save Media**:
   - After selecting topics and chemicals, save the media metadata. The media will now appear in the OData feed.

### Example Output for Exposed Media:

```json
[
  {
    "ID": 168,
    "Title": "Sample Media Document",
    "Description": "Description of the media",
    "URL": "https://your-site.com/uploads/sample-media.pdf",
    "Topics": ["Bioaccumulation", "Mercury"],
    "Chemicals": ["DDT", "Lead"],
    "MimeType": "application/pdf"
  }
]
```

---

## Metadata Exposed for Media

- **ID**: Unique identifier for the media item.
- **Title**: Title of the media item.
- **Description**: Description of the media.
- **URL**: Direct URL to the media file.
- **Topics**: Topics assigned to the media.
- **Chemicals**: Chemicals assigned to the media.
- **MimeType**: MIME type of the media file (e.g., `application/pdf`, `image/jpeg`).

---

## Installation

1. **Backup WordPress Database**  
   Although this module installation is low-risk, it’s good practice to perform a full database backup beforehand using a Backup/Restore plugin (e.g., UpdraftPlus).

2. **Download the Plugin**  
   - Visit https://github.com/brs-conventions-official/RC_wordpress_plugin.  
   - Click on the **Code** menu and select **Download ZIP** to download the `RC_wordpress_plugin-main.zip` file.

3. **Install the Plugin**  
   - Go to **Add New Plugin** > **Upload Plugin**, select the file, and click **Install Now**.

4. **Activate the Plugin**  
   - In the WordPress admin dashboard, navigate to the **Plugins** section.  
   - Find the **BRSCHM** plugin in the list and click **Activate** to enable it.

5. **Send an Email to the BRS Secretariat**  
   - Notify **claire.morel@un.org** to activate synchronization with the BRS portal.

Your BRSCHM plugin should now be installed and active on your WordPress site.

---

## Changelog

### 1.2
- Initial release with OData v4 support for exposing posts, custom fields, and media attachments.
- Added support for tagging and exposing media files directly with topics and chemicals.

---

## License

This plugin is licensed under the GPLv2 or later license. For more details, visit the [GPLv2 license](https://www.gnu.org/licenses/gpl-2.0.html).
