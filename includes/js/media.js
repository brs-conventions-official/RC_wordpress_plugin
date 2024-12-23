// media.js

document.addEventListener('DOMContentLoaded', () => {

    // Function to open a modal in the media context
    window.openMediaModal = function(modalId) {
        const modal = document.getElementById(modalId);
        const overlay = document.getElementById('modal-overlay');

        let attachmentId = get_media_id();

        if (modal) {
            const mediaFrame = document.querySelector('.media-modal');
            if (mediaFrame) {
                mediaFrame.appendChild(modal);
            }
            modal.style.display = 'block';
            modal.style.zIndex = '160000'; // High z-index to ensure visibility
            modal.classList.add('media-context'); // Add a specific class for media context
            if (overlay) overlay.style.display = 'block';

            // Load the specific topics and chemicals for the selected media
            jQuery.ajax({
                url: brschm_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_media_tags',
                    attachment_id: attachmentId,
                    nonce: brschm_ajax.nonce,
                },
                success: function(response) {
                    if (response.success) {
                        const { topics, chemicals } = response.data;

                        // Populate Topics
                        document.querySelectorAll('input[name="media_topics[]"]').forEach((checkbox) => {
                            checkbox.checked = topics.includes(checkbox.value);
                        });

                        // Populate Chemicals
                        document.querySelectorAll('input[name="media_chemicals[]"]').forEach((checkbox) => {
                            checkbox.checked = chemicals.includes(checkbox.value);
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error loading tags:", error);
                }
            });
        }
    };

    // Function to close a modal in the media context
    window.closeMediaModal = function(modalId) {
        const modal = document.getElementById(modalId);
        const overlay = document.getElementById('modal-overlay');
        if (modal) {
            modal.style.display = 'none';
            modal.classList.remove('media-context'); // Remove the specific class for media context
            if (overlay) overlay.style.display = 'none';
        }
    };

    // Save topics and chemicals for media
    function saveMediaTags(modalId) {
        let selectedTopics = [];
        let selectedChemicals = [];

        let attachmentId = get_media_id();
        
        document.querySelectorAll('input[name="media_topics[]"]:checked').forEach((checkbox) => {
            selectedTopics.push(checkbox.value);
        });

        document.querySelectorAll('input[name="media_chemicals[]"]:checked').forEach((checkbox) => {
            selectedChemicals.push(checkbox.value);
        });

        // Send AJAX request to save media tags
        jQuery.ajax({
            url: brschm_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'save_media_topics_chemicals',
                attachment_id: attachmentId,
                topics: selectedTopics,
                chemicals: selectedChemicals,
                nonce: brschm_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    console.log('Media tags saved successfully.');
                    closeMediaModal(modalId); // Close the modal after saving
                    //alert('Media tags saved successfully!');
                } else {
                    alert('Failed to save media tags.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error saving media tags:', error);
            }
        });
    }

    // Attach event listeners to save buttons in modals
    const saveButtons = document.querySelectorAll('.brschm_media-save-tags');
    if (saveButtons) {
        saveButtons.forEach((button) => {
            button.addEventListener('click', function() {
                const modalId = this.getAttribute('data-modal-id');
                saveMediaTags(modalId);
            });
        });
    }

    // Close modal when clicking outside of it
    window.onclick = function(event) {
        const topicsModal = document.getElementById('media-topics-modal');
        const chemicalsModal = document.getElementById('media-chemicals-modal');

        if (event.target === topicsModal) {
            closeMediaModal('media-topics-modal');
        }

        if (event.target === chemicalsModal) {
            closeMediaModal('media-chemicals-modal');
        }
    };

});

function get_media_id() {
    // Extract attachment ID from the URL
    let urlParams = new URLSearchParams(window.location.search);
    let attachmentId = urlParams.get('item'); // `item` contains the attachment ID

    if (!attachmentId) {
        console.error('Attachment ID not found in the URL. Ensure you are editing a media item.');
        return; // Exit if the attachment ID is not found
    }
    console.log('Attachment ID:', attachmentId);
    return attachmentId;
}
