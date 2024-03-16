$(document).ready(function() {
    let currentSectionId = null;

    // Function Definitions

    /* Used to update info from filter modal */
    function updateSectionScheduleInfo() {
        var acadYear = $('#filter_acad_year').val();
        var term = $('#filter_term').val();
        var programText = $('#filter_program').find(":selected").text();
        var yearLevel = $('#filter_year_level').val();

        $('#display_acad_year').text(acadYear);
        $('#display_term').text(term);
        $('#display_program').text(programText === 'Select All' ? "All Programs" : programText);
        $('#display_year_level').text(yearLevel);
    }

    function fetchAndUpdateSections() {
        $.ajax({
            url: sectionsUrl,
            type: 'GET',
            data: {
                acad_year: $('#filter_acad_year').val(),
                term: $('#filter_term').val(),
                program: $('#filter_program').val(),
                year_level: $('#filter_year_level').val(),
            },
            success: function(data) {
                updateSectionButtons(data.sections);
                selectSection(data.sections[0].section_id);
                fetchProgramSubjects();
            },
            error: function(xhr, status, error) {
                updateSectionButtonsError();
                console.error("Error fetching sections:", error);
            }
        });
    }

    function fetchProgramSubjects() {
        $.ajax({
            url: programSubjectsUrl,
            type: 'GET',
            data: {
                acad_year: $('#filter_acad_year').val(),
                term: $('#filter_term').val(),
                program: $('#filter_program').val(),
                year_level: $('#filter_year_level').val(),
            },
            success: function(response) {
                updateTableWithProgramSubjects(response);
            },
            error: function(error) {
                console.error("Error fetching program subjects:", error);
            }
        });
    }

    function updateTableWithProgramSubjects(data) {
        var tbody = $('table > tbody');
        console.log(data);
        tbody.empty(); 
    
        data.forEach(function(subject) {
            var row = $('<tr></tr>');
            row.append($(`<td class="border font-semibold border-gray-300" subject_id="${subject.subject_id}" subject_code="${subject.subject_code}"></td>`).text(subject.subject_code));
            row.append($(`<td class="border border-gray-300" subject_name="${subject.subject_name}"></td>`).text(subject.subject_name));

            $.ajax({
                url: fetchSectionSchedule,
                type: 'GET',
                data: {
                    section_id: currentSectionId,
                    subject_id: subject.subject_id
                },
                success: function(scheduleData) {
                    var schedule = scheduleData[0] || {};
                    var f2fTimeSchedule = formatTime(schedule.start_time_f2f, schedule.end_time_f2f);
                    var onlineTimeSchedule = formatTime(schedule.start_time_online, schedule.end_time_online);
                    var formattedClassDaysF2F = formatDays(schedule.class_days_f2f || '[]');
                    var formattedClassDaysOnline = formatDays(schedule.class_days_online || '[]');
                    row.append($('<td class="border border-gray-300"></td>').text(formattedClassDaysF2F || 'N/A'));
                    row.append($('<td class="border border-gray-300"></td>').text(f2fTimeSchedule || 'N/A'));
                    row.append($('<td class="border border-gray-300"></td>').text(formattedClassDaysOnline || 'N/A'));
                    row.append($('<td class="border border-gray-300"></td>').text(onlineTimeSchedule || 'N/A'));
                    row.append($('<td class="border border-gray-300"></td>').text(schedule.professor_name || 'N/A'));
                    row.append($('<td class="border border-gray-300"></td>').text(schedule.room || 'N/A'));
                    row.append($('<td class="border border-gray-300"></td>').text(schedule.class_limit || 'N/A'));
                    row.append($("<td class='border border-gray-300'></td>").append("<button class='manageButton bg-green-500 w-full text-white text-xs px-1 py-1 rounded hover:bg-green-600 transition ease-in-out duration-150'>Manage</button>"));
                    tbody.append(row);
                },
                error: function(error) {
                    console.error("Error fetching schedule details:", error);
                    row.append($('<td class="border border-gray-300 text-center" colspan="7"></td>').text('Schedule Information Not Set'));
                    row.append($("<td class='border border-gray-300'></td>").append("<button class='manageButton bg-green-500 w-full text-white text-xs px-1 py-1 rounded hover:bg-green-600 transition ease-in-out duration-150'>Manage</button>"));
                    tbody.append(row);
                }
            });
        });
    }

    function updateSectionButtons(sections) {
        var container = $('#display-sections');
        container.empty();
        sections.forEach(function(section) {
            var button = $('<button/>', {
                text: section.section_name,
                id: 'section-' + section.section_id,
                class: 'bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition ease-in-out duration-150',
                click: function() {
                    selectSection(section.section_id);
                    console.log(`Year level: ${section.year_level}`);
                    console.log(`Section Id: ${section.section_id}`);
                    console.log(`Section name: ${section.section_name}`);
                    fetchProgramSubjects();
                }
            });
            container.append(button);
        });
    }

    function updateSectionButtonsError() {
        var container = $('#display-sections');
        container.empty(); 
        var notice = $('<div/>').text('No sections have been created for the selected academic year, term, and program.');
        notice.addClass('test-red');
        container.append(notice);
    }

    function selectSection(sectionId) {
        currentSectionId = sectionId;
        $('button[id^="section-"]').attr('class', 'bg-gray-500 text-white px-2 py-2 rounded hover:bg-gray-600 transition ease-in-out duration-150');
        $('#section-' + sectionId).attr('class', 'bg-red-500 text-white text-md px-2 py-2 rounded hover:bg-red-600 transition ease-in-out duration-150');
    }

    function formatTime(startTime, endTime) {
        function formatSingleTime(timeStr) {
            let dateTime = new Date('1970-01-01T' + timeStr);
            let options = { hour: 'numeric', minute: 'numeric', hour12: true };
            return new Intl.DateTimeFormat('en-US', options).format(dateTime);
        }

        let formattedStartTime = formatSingleTime(startTime);
        let formattedEndTime = formatSingleTime(endTime);

        return formattedStartTime + ' - ' + formattedEndTime;
    }

    function formatDays(daysJson) {
        const daysArray = JSON.parse(daysJson);

        const abbreviations = daysArray.map(day => {
            switch(day) {
                case 'Monday': return 'MON';
                case 'Tuesday': return 'TUE';
                case 'Wednesday': return 'WED';
                case 'Thursday': return 'THU';
                case 'Friday': return 'FRI';
                case 'Saturday': return 'SAT';
                case 'Sunday': return 'SUN';
                default: return '';
            }
        });

        return abbreviations.join(' ');
    }

    // Event Handlers

    $('#filter-confirm-button').on('click', function() {
        fetchAndUpdateSections();
        updateSectionScheduleInfo();
    });

    $('table').on('click', '.manageButton', function() {
        var subjectIdValue = $(this).closest('td').prevAll('[subject_id]').attr('subject_id');
        $('#subject_id').val(subjectIdValue);
        $('#section_id').val(currentSectionId);
        $('#manageSchedule').show();
    });

    $(document).on('click', '.hide-manage', function() {
        $('#manageSchedule').hide();
    });

    $(document).on('keydown', function(event) {
        if (event.keyCode === 27) {
            $('#manageSchedule').hide();
        }
    });

    $('.assign_section').on('click', function() {
        var form = document.getElementById('createSectionSchedule');
        if(form.checkValidity()) {
            form.method = 'POST';
            form.submit();
        } else {
            alert('Please complete the form to complete section schedule assignment');
        }
    });

    $('#display-sections').on('click', 'button[id^="section-"]', function() {
        var sectionId = this.id.replace('section-', '');
        selectSection(sectionId);
    });

    $('#prof_id').select2({
        width: 'resolve',
        placeholder: "Professor Name",
        allowClear: true,
        minimumInputLength: 1,
        ajax: {
            url: searchFacultyUrl,
            dataType: 'json',
            delay: 20,
            processResults: function(data) {
                return {
                    results: data.map(function(item) {
                        return {
                            id: item.prof_id,
                            text: item.first_name + ' ' + item.last_name
                        };
                    })
                };
            },
            cache: true
        }
    });

    // Initial Calls
    updateSectionScheduleInfo();
    fetchAndUpdateSections();
});