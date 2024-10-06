<?php
// Function to display CHM options (Documents, Events, News, Contacts)
function brschm_display_chm_options($post_id) {
    echo '<div id="chm-options">';
    
    // Radio button style options
    echo '<label><input type="radio" name="chm-options" id="documents" class="chm-radio" value="documents"> Documents</label>';
    echo '<label><input type="radio" name="chm-options" id="events" class="chm-radio" value="events"> Events</label>';
    echo '<label><input type="radio" name="chm-options" id="news" class="chm-radio" value="news"> News</label>';
    echo '<label><input type="radio" name="chm-options" id="contacts" class="chm-radio" value="contacts"> Contacts</label>';
    
    // Hidden buttons for Topics and Chemicals, shown only when "Documents" is selected
    echo '<div id="documents-options" style="display: none; margin-top: 10px;">';
    echo '<button type="button" class="button" id="topics-button" onclick="openModal(\'topics-modal\')">Select Topics</button><br><br>';
    echo '<button type="button" class="button" id="chemicals-button" onclick="openModal(\'chemicals-modal\')">Select Chemicals</button><br><br>';
    echo '</div>';

    // Add the Share on the CHM switch and ensure it's on the same line
    echo '<div id="chm-share-switch" class="chm-switch-container" style="margin-top: 15px; display: flex; align-items: center;">';
    echo '<label for="chm-share-toggle" style="margin-right: 10px;">Share on the CHM: </label>';
    echo '<input type="checkbox" id="chm-share-toggle" class="chm-switch" style="margin-right: 10px;">';
    echo '<span id="chm-switch-status">OFF</span>';
    echo '</div>';

    echo '</div>';
}
