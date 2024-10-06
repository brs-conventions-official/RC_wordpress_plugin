document.addEventListener('DOMContentLoaded', () => {
    const contactMetaBox = document.getElementById('brschm_contact_meta_box');

    // Hide the contact meta box by default
    if (contactMetaBox) {
        contactMetaBox.style.display = 'none';
    }

    // Function to handle the CHM option selection
    function toggleContactFields() {
        const selectedOption = document.querySelector('input[name="chm-options"]:checked').value;

        // Hide the contact meta box by default
        if (contactMetaBox) {
            contactMetaBox.style.display = 'none';
        }

        // Show contact fields only when "Contact" is selected
        if (selectedOption === 'contacts') {
            if (contactMetaBox) {
                contactMetaBox.style.display = 'block';
            }
        }
    }

    // Event listener for the radio button change
    const radioButtons = document.querySelectorAll('input[name="chm-options"]');
    if (radioButtons) {
        radioButtons.forEach((radio) => {
            radio.addEventListener('change', toggleContactFields);
        });
    }

    // Call the function if the post is already assigned to Contact on page load
    toggleContactFields();
});
