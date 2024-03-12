$(document).ready(function() {
    // Parse the URL query parameters
    var urlParams = new URLSearchParams(window.location.search);
    var highlightId = urlParams.get('highlight');

    if (highlightId) {
        // Use jQuery to select the appointment div and add a class or style
        $(`#appointment-${highlightId}`).addClass('bg-rose-600');
        // Optionally, scroll the highlighted appointment into view
        $('html, body').animate({
            scrollTop: $(`#appointment-${highlightId}`).offset().top
        }, 1000);
    }
});