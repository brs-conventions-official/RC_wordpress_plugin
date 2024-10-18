document.addEventListener('DOMContentLoaded', () => {
    const eventRadio = document.getElementById('chm-event-radio');
    const selectedOption = document.querySelector('input[name="chm-options"]:checked').value;
    const eventMetaBox = document.getElementById('brschm_event_meta_box');



    if (eventRadio && eventMetaBox) {
        eventRadio.addEventListener('change', function () {
            if (eventRadio.checked) {
                eventMetaBox.style.display = 'block';
            } else {
                eventMetaBox.style.display = 'none';
            }
        });
    }

    // Hide the event meta box by default
    if (eventMetaBox) {
        eventMetaBox.style.display = 'none';
    }

    // Show event fields only when "Events" is selected
    if (selectedOption && selectedOption === 'events') {
        if (eventMetaBox) {
            eventMetaBox.style.display = 'block';
        }
    }
        

     // Function to dynamically show event fields when the Events option is selected
     window.ensureEventFields = function(postId) {
        const eventMetaBox = document.getElementById('brschm_event_meta_box');
        if (eventMetaBox) {
            eventMetaBox.style.display = 'block'; // Show the event meta box
        }
    };
});
