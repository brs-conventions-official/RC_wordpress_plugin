document.addEventListener('DOMContentLoaded', () => {
    const chmLogo = document.getElementById('chm-logo');
    const chmOptions = document.getElementById('chm-options');

    const preselectedTagElement = document.getElementById('chm-preselected'); // Get the element
    // Check if the chm-preselected element exists
    const preselectedTag = preselectedTagElement ? preselectedTagElement.value : '';
    // chm share switch toggle
    const chmShareSwitch = document.getElementById('chm-share-toggle');
    const chmSwitchStatus = document.getElementById('chm-switch-status');

    // Function to toggle CHM options when the logo is clicked
    function toggleCHMOptions() {


        if (chmOptions.style.display === 'none' || chmOptions.style.display === '') {
            chmOptions.style.display = 'block';
            chmLogo.classList.add('highlight');  // Highlight the logo when options are visible
        } else {
            chmOptions.style.display = 'none';
            chmLogo.classList.remove('highlight');  // Unhighlight the logo when options are hidden
        }
    }

    // Function to handle CHM option selection
    /*//vl241006 matin
    function handleCHMSelection() {
        const selectedOption = document.querySelector('input[name="chm-options"]:checked').value;
        const postId = document.querySelector('input[name="post_ID"]').value;

        const tagMap = {
            'documents': 'document',
            'events': 'event',
            'news': 'news',
            'contacts': 'contact'
        };

        // Send AJAX request to assign the selected tag
        jQuery.ajax({
            url: brschm_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'assign_chm_tag',
                post_id: postId,
                selected_tag: tagMap[selectedOption],
                nonce: brschm_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    console.log('Tag successfully updated');
                    // Update the UI based on the selection
                    updateCHMUI(selectedOption);

                    // If "Events" is selected, ensure event fields are created/visible
                    if (selectedOption === 'events') {
                        ensureEventFields(postId); // Ensure event fields are displayed
                    } else {
                        // Hide the meta box if it's not Events
                        const eventMetaBox = document.getElementById('brschm_event_meta_box');
                        if (eventMetaBox) {
                            eventMetaBox.style.display = 'none';
                        }
                    }
                } else {
                    alert('Failed to update the tag.');
                }
            },
            error: function(xhr, status, error) {
                console.log("AJAX Error:", error, xhr.responseText);
            }
        });
    }
*/
function handleCHMSelection() {
    const selectedOption = document.querySelector('input[name="chm-options"]:checked').value;
    const postId = document.querySelector('input[name="post_ID"]').value;

    const tagMap = {
        'documents': 'document',
        'events': 'event',
        'news': 'news',
        'contacts': 'contact'
    };

    // Send AJAX request to assign the selected tag
    jQuery.ajax({
        url: brschm_ajax.ajax_url,
        type: 'POST',
        data: {
            action: 'assign_chm_tag',
            post_id: postId,
            selected_tag: tagMap[selectedOption],
            nonce: brschm_ajax.nonce
        },
        success: function(response) {
            if (response.success) {
                console.log('Tag successfully updated');
                // Update the UI based on the selection
                updateCHMUI(selectedOption);

                // Handling for "Events"
                const eventMetaBox = document.getElementById('brschm_event_meta_box');
                if (selectedOption === 'events') {
                    ensureEventFields(postId); // Ensure event fields are displayed
                    if (eventMetaBox) {
                        eventMetaBox.style.display = 'block';  // Show the event meta box
                    }
                } else if (eventMetaBox) {
                    eventMetaBox.style.display = 'none';  // Hide the event meta box if not selected
                }

                // Handling for "Contacts"
                const contactMetaBox = document.getElementById('brschm_contact_meta_box');
                if (selectedOption === 'contacts') {
                    ensureContactFields(postId); // Ensure contact fields are displayed
                    if (contactMetaBox) {
                        contactMetaBox.style.display = 'block';  // Show the contact meta box
                    }
                } else if (contactMetaBox) {
                    contactMetaBox.style.display = 'none';  // Hide the contact meta box if not selected
                }

            } else {
                alert('Failed to update the tag.');
            }
        },
        error: function(xhr, status, error) {
            console.log("AJAX Error:", error, xhr.responseText);
        }
    });
}

    // Function to update the UI based on the selected CHM option
    function updateCHMUI(selectedOption) {
        const documentsOptions = document.getElementById('documents-options');
        const eventFields = document.getElementById('event-fields-container');

        if (selectedOption === 'documents') {
            documentsOptions.style.display = 'block';  // Show Topics and Chemicals
            if (eventFields) eventFields.style.display = 'none'; // Hide event fields if visible
        } else if (selectedOption === 'events') {
            documentsOptions.style.display = 'none';  // Hide Topics and Chemicals
            // Event fields are handled in event.js
        } else {
            documentsOptions.style.display = 'none';  // Hide Topics and Chemicals for other selections
            if (eventFields) eventFields.style.display = 'none'; // Hide event fields if not "Events"
        }
    }

    // Function to pre-select the radio button based on existing tags
    function preSelectCHMOption() {
        const preselectedTagInput = document.querySelector('input[name="chm_option_preselected_tag"]');
        if (preselectedTagInput) {
            const existingTag = preselectedTagInput.value;

            // Pre-select the appropriate radio button
            if (existingTag) {
                const optionToSelect = document.getElementById(existingTag);
                if (optionToSelect) {
                    optionToSelect.checked = true;
                    updateCHMUI(existingTag);  // Update UI without reloading
                }
            }
        }
    }

    // Global function to open modal
    window.openModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'block';
        }
    };

    // Global function to close modal and save tags
    window.closeModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';

            // Gather selected topics and chemicals
            let selectedTopics = [];
            let selectedChemicals = [];

            document.querySelectorAll('input[name="brschm_topics[]"]:checked').forEach((checkbox) => {
                selectedTopics.push(checkbox.value);
            });

            document.querySelectorAll('input[name="brschm_chemicals[]"]:checked').forEach((checkbox) => {
                selectedChemicals.push(checkbox.value);
            });

            // Send AJAX request to save the selected topics and chemicals
            const postId = document.querySelector('input[name="post_ID"]').value;

            jQuery.ajax({
                url: brschm_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'save_topics_chemicals',
                    post_id: postId,
                    topics: selectedTopics,
                    chemicals: selectedChemicals,
                    nonce: brschm_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        // Reload the page to show the updated tags in the post editor
                        window.location.reload();
                    } else {
                        alert('Failed to save the tags.');
                    }
                }
            });
        }
    };

    // Close modal when clicking outside of it
    window.onclick = function(event) {
        const topicsModal = document.getElementById('topics-modal');
        const chemicalsModal = document.getElementById('chemicals-modal');
        
        if (event.target === topicsModal) {
            topicsModal.style.display = 'none';
        }
        
        if (event.target === chemicalsModal) {
            chemicalsModal.style.display = 'none';
        }
    };

    // Ensure that elements exist before adding event listeners
    const radioButtons = document.querySelectorAll('.chm-radio');

    if (chmLogo) {
        chmLogo.addEventListener('click', toggleCHMOptions);
    }

    if (radioButtons) {
        radioButtons.forEach((radio) => {
            radio.addEventListener('change', handleCHMSelection);
        });
    }

//*
      // Call the function to pre-select the radio button if the post already has a tag assigned
    //preSelectCHMOption();
 //*/

   // /*vl240927 
        // Function to toggle CHM tag and button highlight
        function toggleCHMTag() {
            const postId = document.querySelector('input[name="post_ID"]').value;
    
            // Determine if the CHM button is highlighted (tag already exists)
            const isHighlighted = chmLogo.classList.contains('highlight');
    
            // AJAX request to add or remove the CHM tag
            /*//vl240927
            jQuery.ajax({
                url: brschm_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: isHighlighted ? 'remove_chm_tag' : 'add_chm_tag',
                    post_id: postId,
                    nonce: brschm_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        if (isHighlighted) {
                            // Unhighlight button and hide CHM options
                            chmLogo.classList.remove('highlight');
                            chmOptions.style.display = 'none';
                        } else {
                            // Highlight button and show CHM options
                            chmLogo.classList.add('highlight');
                            chmOptions.style.display = 'block';
                        }
                    } else {
                        alert('Failed to update the CHM tag.');
                    }
                },
                error: function(xhr, status, error) {
                    console.log("AJAX Error:", error, xhr.responseText);
                }
            });
            */
        }
    
        // Preselect CHM if the tag is already assigned
        function preSelectCHM() {
            if (preselectedTag === 'chm') {
                chmShareSwitch.checked = true;
                chmSwitchStatus.textContent = 'ON';
                chmLogo.classList.add('highlight');
                chmOptions.style.display = 'block';
                // Call the function to pre-select the radio button if the post already has a tag assigned
                preSelectCHMOption();
            } else {
                if (chmOptions && chmOptions.style) {
                    chmOptions.style.display = 'none';
                    chmShareSwitch.checked = false;
                    chmSwitchStatus.textContent = 'OFF';
                }
            }
        }
    
        // Event listener for the CHM button
        if (chmLogo) {
            chmLogo.addEventListener('click', toggleCHMTag);
        }

        // Event listener for the CHM Share switch
        if (chmShareSwitch) {
            chmShareSwitch.addEventListener('change', toggleCHMShare);
        }

        // Pre-select CHM button if the tag exists
        preSelectCHM();
     ///vl240927



    // Attach event listeners to close buttons for modals
    const closeButtons = document.querySelectorAll('.close');
    if (closeButtons) {
        closeButtons.forEach((closeBtn) => {
            closeBtn.addEventListener('click', function() {
                closeModal(this.closest('.modal').id);
            });
        });
    }


    // Function to handle the CHM Share toggle switch
    function toggleCHMShare() {
        const postId = document.querySelector('input[name="post_ID"]').value;
        const isChecked = chmShareSwitch.checked;

        jQuery.ajax({
            url: brschm_ajax.ajax_url,
            type: 'POST',
            data: {
                action: isChecked ? 'add_chm_tag' : 'remove_chm_tag',
                post_id: postId,
                nonce: brschm_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    chmSwitchStatus.textContent = isChecked ? 'ON' : 'OFF';
                } else {
                    alert('Failed to update the CHM tag.');
                }
            },
            error: function(xhr, status, error) {
                console.log("AJAX Error:", error, xhr.responseText);
            }
        });
    }
});
