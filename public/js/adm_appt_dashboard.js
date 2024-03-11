$(document).ready(function() {
    var currentQueueSchedType = 'today';
    var currentPendingDayType = 'oneDay';
    var switchIntervalQueueSched;
    var switchIntervalPendingDays;
    // alert(appointmentsQueueUrl);

    function fetchAndDisplayQueue(queueSchedType) {
        $.ajax({
            url: appointmentsQueueUrl,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                // $('.appointments-queue').html('<div>Has appointments in queue</div>'); 
                displayQueue(data, queueSchedType);
                toggleButtonStylesQueue(queueSchedType, 'queueSched');
            },
            error: function() {
                $('.appointments-queue').html('<div>No appointments in queue</div>'); 
            }
        });
    }

    function fetchAndDisplayPending(pendingDayType) {
        $.ajax({
            url: appointmentsQueueUrl,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                displayPending(data, pendingDayType);
                toggleButtonStylesPending(pendingDayType, 'pendingDay')
            },
            error: function() {
                $('.appointments-pending').html('<div>No pending appointments</div>'); 
            }
        });
    }

    // Function to display queueSched
    function displayQueue(data, queueSchedType) {
        var container = $('.appointments-queue');
        container.fadeOut(400, function() {
            container.empty(); 
            var queueSched = queueSchedType === 'today' ? data.today :
                (queueSchedType === 'tomorrow' ? data.tomorrow : 
                (queueSchedType === 'thisWeek' ? data.thisWeek : data.today));
            var queueSchedArray = Object.values(queueSched); 
            // console.log(queueSched);
            // console.log(queueSchedArray);

            if(queueSchedArray.length > 0) {
                queueSchedArray.forEach(function(qs) {
                    var appointee = qs.student_last_name.slice(0,1) + '. ' + qs.student_first_name;
                    var queueSchedInfo = `<a href="/admin/appointments/manage/${qs.id}" target="_blank"><div class="mb-2 p-1"><p class="text-xs font-semibold">${qs.student_number}-${appointee}</p> <p class="text-sm">${qs.service_name}</p></div></a>`;
                    var test = `<div>Has appointments in queue</div>`
                    container.append(queueSchedInfo);
                    // console.log('true');
                });
            } else {
                container.html('<div class="text-sm">No appointments in queue.</div>'); 
            }
            container.fadeIn(400);
        });
    }

    function displayPending(data, pendingDayType) {
        var container = $('.appointments-pending');
        container.fadeOut(400, function() {
            container.empty(); 
            var pendingDay = pendingDayType === 'oneDay' ? data.pendingOneDay :
                (pendingDayType === 'twoDays' ? data.pendingTwoDays : 
                (pendingDayType === 'beyondTwoDays' ? data.pendingBeyondTwoDays : data.pendingOneDay));
            var pendingDayArray = Object.values(pendingDay); 
            // console.log(pendingDay);
            // console.log(pendingDayArray);

            if(pendingDayArray.length > 0) {
                pendingDayArray.forEach(function(pd) {
                    var appointee = pd.student_last_name.slice(0,1) + '. ' + pd.student_first_name;
                    var pendingDayInfo = `<a href="/admin/appointments/manage/${pd.id}" target="_blank"><div class="mb-2 p-1"><p class="text-xs font-semibold">${pd.student_number}-${appointee}</p> <p class="text-sm">${pd.service_name}</p></div></a>`;
                    var test = `<div>Has pending appointments</div>`
                    container.append(pendingDayInfo);
                    // console.log('true');
                });
            } else {
                container.html('<div class="text-sm">No pending appointments.</div>'); 
            }
            container.fadeIn(400);
        });
    }

    function toggleButtonStylesPending(pendingDayType, target) {
        var oneDayButton = $('.one-day-button'),
            twoDaysButton = $('.two-days-button'),
            beyondTwoDaysButton = $('.beyond-two-days-button');
        
        if(pendingDayType === 'oneDay') {
            oneDayButton.addClass('bg-red-500 hover:bg-red-600').removeClass('bg-gray-500 hover:bg-gray-600');
            twoDaysButton.removeClass('bg-red-500 hover:bg-red-600').addClass('bg-gray-500 hover:bg-gray-600');
            beyondTwoDaysButton.removeClass('bg-red-500 hover:bg-red-600').addClass('bg-gray-500 hover:bg-gray-600');
        } else if (pendingDayType === 'twoDays'){
            twoDaysButton.addClass('bg-red-500 hover:bg-red-600').removeClass('bg-gray-500 hover:bg-gray-600');
            oneDayButton.removeClass('bg-red-500 hover:bg-red-600').addClass('bg-gray-500 hover:bg-gray-600');
            beyondTwoDaysButton.removeClass('bg-red-500 hover:bg-red-600').addClass('bg-gray-500 hover:bg-gray-600');
        } else {
            beyondTwoDaysButton.addClass('bg-red-500 hover:bg-red-600').removeClass('bg-gray-500 hover:bg-gray-600');
            twoDaysButton.removeClass('bg-red-500 hover:bg-red-600').addClass('bg-gray-500 hover:bg-gray-600');
            oneDayButton.removeClass('bg-red-500 hover:bg-red-600').addClass('bg-gray-500 hover:bg-gray-600');
        }
    }

    function toggleButtonStylesQueue(queueSchedType, target) {
        var todayButton = $('.today-button'),
            tomorrowButton = $('.tomorrow-button'),
            thisWeekButton = $('.thisWeek-button');
        
        if(queueSchedType === 'today') {
            todayButton.addClass('bg-red-500 hover:bg-red-600').removeClass('bg-gray-500 hover:bg-gray-600');
            tomorrowButton.removeClass('bg-red-500 hover:bg-red-600').addClass('bg-gray-500 hover:bg-gray-600');
            thisWeekButton.removeClass('bg-red-500 hover:bg-red-600').addClass('bg-gray-500 hover:bg-gray-600');
        } else if (queueSchedType === 'tomorrow'){
            tomorrowButton.addClass('bg-red-500 hover:bg-red-600').removeClass('bg-gray-500 hover:bg-gray-600');
            todayButton.removeClass('bg-red-500 hover:bg-red-600').addClass('bg-gray-500 hover:bg-gray-600');
            thisWeekButton.removeClass('bg-red-500 hover:bg-red-600').addClass('bg-gray-500 hover:bg-gray-600');
        } else {
            thisWeekButton.addClass('bg-red-500 hover:bg-red-600').removeClass('bg-gray-500 hover:bg-gray-600');
            tomorrowButton.removeClass('bg-red-500 hover:bg-red-600').addClass('bg-gray-500 hover:bg-gray-600');
            todayButton.removeClass('bg-red-500 hover:bg-red-600').addClass('bg-gray-500 hover:bg-gray-600');
        }
    }

    function startSwitchingQueueSched() {
        if (switchIntervalQueueSched) {
            clearInterval(switchIntervalQueueSched);
        }
        switchIntervalQueueSched = setInterval(function() {
            currentQueueSchedType = currentQueueSchedType === 'today' ? 'tomorrow' : 
            (currentQueueSchedType === 'tomorrow' ? 'thisWeek' : 'today');
            fetchAndDisplayQueue(currentQueueSchedType);
        }, 14000); 
    }

    function startSwitchingPendingDays() {
        if (switchIntervalPendingDays) {
            clearInterval(switchIntervalPendingDays);
        }
        switchIntervalPendingDays = setInterval(function() {
            currentPendingDayType = currentPendingDayType === 'oneDay' ? 'twoDays' : 
            (currentPendingDayType === 'twoDays' ? 'beyondTwoDays' : 'oneDay');
            fetchAndDisplayPending(currentPendingDayType);
        }, 14000); 
    }
    
    function stopSwitchingQueue() {
        if (switchIntervalQueueSched) {
            clearInterval(switchIntervalQueueSched);
        }
    }

    function stopSwitchingPending() {
        if (switchIntervalPendingDays) {
            clearInterval(switchIntervalPendingDays);
        }
    }

    $('.queue-container').mouseenter(function() {
        stopSwitchingQueue();
    }).mouseleave(function() {
        startSwitchingQueueSched();
    });

    $('.pending-container').mouseenter(function() {
        stopSwitchingPending();
    }).mouseleave(function() {
        startSwitchingPendingDays();
    });

    fetchAndDisplayQueue(currentQueueSchedType);
    fetchAndDisplayPending(currentPendingDayType);
    startSwitchingQueueSched();
    startSwitchingPendingDays();

    function updateButtonStatesQueue(activeButton) {
        $(".actv-queue-btn").removeClass('active-queueSched bg-red-500').addClass('bg-gray-500');
        $(activeButton).addClass('active-queueSched bg-red-500').removeClass('bg-gray-500');
        fetchAndDisplayQueue(currentQueueSchedType);
    }

    function updateButtonStatesPending(activeButton) {
        $(".actv-pending-btn").removeClass('active-pendingDay bg-red-500').addClass('bg-gray-500');
        $(activeButton).addClass('active-pendingDay bg-red-500').removeClass('bg-gray-500');
        fetchAndDisplayPending(currentPendingDayType);
    }

    $(".today-button").click(function() {
        currentQueueSchedType = 'today';
        updateButtonStatesQueue(this); 
    });

    $(".tomorrow-button").click(function() {
        currentQueueSchedType = 'tomorrow';
        updateButtonStatesQueue(this); 
    });

    $(".thisWeek-button").click(function() {
        currentQueueSchedType = 'thisWeek';
        updateButtonStatesQueue(this); 
    });

    /* Pending */

    $(".one-day-button").click(function() {
        currentPendingDayType = 'oneDay';
        updateButtonStatesPending(this); 
    });

    $(".two-days-button").click(function() {
        currentPendingDayType = 'twoDays';
        updateButtonStatesPending(this); 
    });

    $(".beyond-two-days-button").click(function() {
        currentPendingDayType = 'beyondTwoDays';
        updateButtonStatesPending(this); 
    });
});