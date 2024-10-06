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