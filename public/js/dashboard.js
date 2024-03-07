$(document).ready(function() {
    var currentClassType = 'F2F';
    var switchInterval;

    function fetchAndDisplayClasses(classType) {
        $.ajax({
            url: activeClassesUrl,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                displayClasses(data, classType);
                toggleButtonStyles(classType, 'class');
            },
            error: function() {
                $('.active-classes').html('<div>No active classes</div>'); 
            }
        });
    }

    // Function to display classes
    function displayClasses(data, classType) {
        var container = $('.active-classes');
        container.fadeOut(400, function() {
            container.empty(); 
            var classes = classType === 'F2F' ? data.activeF2FClasses : data.activeOnlineClasses;
            var classesArray = Object.values(classes); 

            if(classesArray.length > 0) {
                classesArray.forEach(function(cl) {
                    var sec_name = cl.section_name.slice(0,3) + ' ' + cl.section_name.slice(-1);
                    var classInfo = `<div class="mb-2 p-2"><span class="text-md font-semibold">${sec_name} ${cl.subject_name}</span> <p class="text-sm">(${cl.time}) ${cl.professor}</p></div>`;
                    container.append(classInfo);
                });
            } else {
                container.html('<div class="text-sm">No active classes</div>'); 
            }
            container.fadeIn(400);
        });
    }

    function toggleButtonStyles(classType, target) {
        var f2fButton = $('.active-f2f'),
            onlineButton = $('.active-ol');
        
        if(classType === 'F2F') {
            f2fButton.addClass('bg-red-500 hover:bg-red-600').removeClass('bg-gray-500 hover:bg-gray-600');
            onlineButton.removeClass('bg-red-500 hover:bg-red-600').addClass('bg-gray-500 hover:bg-gray-600');
        } else {
            onlineButton.addClass('bg-red-500 hover:bg-red-600').removeClass('bg-gray-500 hover:bg-gray-600');
            f2fButton.removeClass('bg-red-500 hover:bg-red-600').addClass('bg-gray-500 hover:bg-gray-600');
        }
    }

    function startSwitchingClasses() {
        if (switchInterval) {
            clearInterval(switchInterval);
        }
        switchInterval = setInterval(function() {
            currentClassType = currentClassType === 'F2F' ? 'Online' : 'F2F';
            fetchAndDisplayClasses(currentClassType);
        }, 14000); 
    }

    function stopSwitchingClasses() {
        if (switchInterval) {
            clearInterval(switchInterval);
        }
    }
    
    startSwitchingClasses();

    getQuotes();
    
    fetchAndDisplayClasses(currentClassType );

    $('.actv-class-btn').click(function() {
        var classType = $(this).hasClass('active-f2f') ? 'F2F' : 'Online';
        currentClassType = classType;
        fetchAndDisplayClasses(classType);
        startSwitchingClasses();
    });

    $('.active-classes-container').mouseenter(function() {
        stopSwitchingClasses();
    }).mouseleave(function() {
        startSwitchingClasses();
    });

    function getQuotes()
    {
        $.ajax({
            url: activeQuoteUrl,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                displayQuote(data);
            },
            error: function() {
                $('.quote-container').html('<div>No quote available</div>');
            }
        });
    }

    function displayQuote(data) {
        var container = $('.quote-container');
        container.empty(); // Clear previous content if any
        var quoteContent = `<div class="pr-8"><p class="quote text-sm">${data.quote}</p><em class="author pl-40 text-xs">- ${data.author}</em></div>`;
        container.html(quoteContent);
    }

});