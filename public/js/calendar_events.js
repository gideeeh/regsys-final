$(document).ready(function() {
    var currentEventType = 'important';
    var switchInterval;

    function fetchAndDisplayCalendarEvents(eventType) {
        $.ajax({
            url: activeEventsUrl,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                displayEvents(data, eventType);
                toggleEventButtonStyles(eventType);
            },
            error: function() {
                $('.active-event').html('<div>No active events</div>');
            }
        });
    }

    //Function to display Caeldnar events
    function displayEvents(data, eventType) {
        var container = $('.active-events');
        container.fadeOut(400, function() {
            container.empty();
            var events = data[eventType];
            var eventsArray = Object.values(events);

            if (eventsArray.length > 0) {
                eventsArray.forEach(function(ev) {
                    var eventInfo = `<div class="mb-2  p-2"><span class="text-md font-semibold">${ev.title}</span> <p class="text-sm">${ev.comments}</p></div>`;
                    container.append(eventInfo);
                });
            } else {
                container.html('<div class="text-sm">No active events</div>');
            }
            container.fadeIn(400);
        });
    }

    function toggleEventButtonStyles(eventType) {
        $('button.actv-event-btn').each(function() {
            if ($(this).data('eventType') === eventType) {
                $(this).removeClass('bg-gray-500 hover:bg-gray-600').addClass('bg-red-500 hover:bg-red-600');
            } else {
                $(this).removeClass('bg-red-500 hover:bg-red-600').addClass('bg-gray-500 hover:bg-gray-600');
            }
        });
    }

    function startSwitchingEvents() {
        if (switchInterval) {
            clearInterval(switchInterval);
        }
        switchInterval = setInterval(function() {
            // var eventTypes = ['important', 'today', 'this_week', 'this_month'];
            var eventTypes = ['important', 'today', 'this_week'];
            var currentIndex = eventTypes.indexOf(currentEventType);
            currentEventType = eventTypes[(currentIndex + 1) % eventTypes.length];
            fetchAndDisplayCalendarEvents(currentEventType);
        }, 14000);
    }

    function stopSwitchingEvents() {
        if (switchInterval) {
            clearInterval(switchInterval);
        }
    }

    startSwitchingEvents();
    fetchAndDisplayCalendarEvents(currentEventType);

    $('button.actv-event-btn').click(function() {
        var eventType = $(this).data('eventType');
        currentEventType = eventType;
        fetchAndDisplayCalendarEvents(eventType);
        startSwitchingEvents();
    });

    $('.active-events-container').mouseenter(function() {
        stopSwitchingEvents();
    }).mouseleave(function() {
        startSwitchingEvents();
    });
});