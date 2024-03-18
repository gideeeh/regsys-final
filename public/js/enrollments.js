$(document).ready(function() {
    let $enrollSubjects = $('#enroll_subjects');
    let selectedSubjects = [];
    let selectedSecSubIds = [];
    // let selections = {}; 
    let selections = [];

    function logCurrentInputs() {
        var inputs = $('#enrollmentForm').find('input, select').serializeArray();
        console.log("Current Form Inputs:", inputs);
        console.log("Selected Subjects:", selectedSubjects);
    }

    $('.log-data').on('click', function() {
        updateSelectedSubjects();
        logCurrentInputs();
    });

    $('#enroll_subjects').select2({
        width: 'resolve',
        placeholder: "Select Subject to Enroll",
        allowClear: true,
        minimumInputLength: 1,
        ajax: {
            url: getSubjectsUrl,
            dataType: 'json',
            delay: 250,
            processResults: function (data) {
                return {
                    results: data.map(function (item) {
                        return {
                            id: item.subject_id,
                            text: item.subject_code + ' - ' + item.subject_name,
                            subject_code: item.subject_code,
                            subject_name: item.subject_name,
                            units_lec: item.units_lec,
                            units_lab: item.units_lab,
                            total_units: item.units_lec + item.units_lab
                        };
                    })
                };
            },
            cache: true
        }
    }).on("select2:select", function(e) {
        let subjectDetails = e.params.data;
        selectedSubjects.push({
            subject_id: subjectDetails.id,
        });

        let uniqueSectionSelectId = 'select_section_' + Date.now();

        let newRow = `<tr data-subject-id="${subjectDetails.id}">
            <td class="border border-gray-300 text-center""><strong>${subjectDetails.subject_code}</strong></td>
            <td class="border border-gray-300 text-center"">${subjectDetails.subject_name}</td>
            <td class="border border-gray-300 text-center"><p class="section-name"></p><select id="${uniqueSectionSelectId}" name="select_section[]" style="width: 100%;"></select></td>
            <td class="border border-gray-300 text-center">${subjectDetails.units_lec}</td>
            <td class="border border-gray-300 text-center"">${subjectDetails.units_lab}</td>
            <td class="f2f-days-cell border border-gray-300 text-center""></td>
            <td class="f2f-time-cell border border-gray-300 text-center""></td>
            <td class="online-days-cell border border-gray-300 text-center""></td>
            <td class="online-time-cell border border-gray-300 text-center""></td>
            <td class="border border-gray-300">
                <button type="button" class="remove-subject bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600 transition ease-in-out duration-150">Remove</button>
            </td>
        </tr>`;

        $("table tbody").prepend(newRow);

        initializeSectionSelect2(`#${uniqueSectionSelectId}`);

        $enrollSubjects.val(null).trigger('change');
        updateTotalUnits();
    });

    $('table').on('click', '.remove-subject', function() {
        let subjectIdToRemove = $(this).closest('tr').data('subjectId');
        $(this).closest('tr').remove();

        selections = selections.filter(selection => selection.subject_id !== subjectIdToRemove.toString());

        console.log("Selections after removal:", selections);
        selectedSubjects = selectedSubjects.filter(subject => subject.subject_id != subjectIdToRemove);

        updateTotalUnits();
        updateSelectedSubjects();
    });

    $('.enroll').on('click', function() {
        updateSelectedSubjects();
        var form = document.getElementById('enrollmentForm');
        if (form.checkValidity()) {
            if (selectedSubjects.length > 0) {
                form.submit();
            } else {
                alert("Please select at least one subject to enroll.");
            }
        } else {
            form.reportValidity();
        }
    });

    $('.validate').on('click', function() {
        console.log("Selected sec_sub_ids:", selectedSubjects.map(s => s.sec_sub_id));
    });

    function updateTotalUnits() {
        let totalLec = 0;
        let totalLab = 0;

        $("table tbody tr").each(function() {
            let lecUnits = parseInt($(this).find("td:nth-child(4)").text()) || 0;
            let labUnits = parseInt($(this).find("td:nth-child(5)").text()) || 0;

            totalLec += lecUnits;
            totalLab += labUnits;
        });

        let totalUnits = totalLec + totalLab;

        $(".total-units-lec").text(`Total Units(Lec): ${totalLec}`);
        $(".total-units-lab").text(`Total Units(Lab): ${totalLab}`);
        $(".total-units").text(`Total Units: ${totalUnits}`);
    }

    function updateSelectedSubjects() {
        $("table tbody tr").each(function() {
            let subjectId = $(this).data('subjectId');
            let secSubId = $(this).find('input[type="hidden"][name="sec_sub_ids[]"]').val();
            if (subjectId && secSubId) {
                selectedSubjects.push({
                    subject_id: subjectId,
                    sec_sub_id: secSubId,
                });
            }
        });
        $('#selectedSubjectsInput').val(JSON.stringify(selectedSubjects));
    }

    $('#stu_to_enroll').select2({
        width: 'resolve',
        placeholder: "Student Name or Student Number",
        allowClear: true,
        minimumInputLength: 1,
        ajax: {
            url: getStudentsUrl ,
            dataType: 'json',
            delay: 20,
            processResults: function (data) {
                return {
                    results: data.map(function (item) {
                        return {
                            id: item.student_id,
                            text: item.student_number + ' - ' + item.first_name + ' ' + item.last_name
                        };
                    })
                };
            },
            cache: true
        }
    }).on("select2:select", function(e) {
        var studentId = e.params.data.id;
        $.ajax({
            url: `/admin/students/get-students/${studentId}`,
            type: "GET",
            success: function(response) {
                $('#student_id').val(response.student_id);
                $('#first_name').val(response.first_name);
                $('#middle_name').val(response.middle_name);
                $('#last_name').val(response.last_name);
                $('#suffix').val(response.suffix);
                $('#student_number').val(response.student_number);
                $('#studentName').text(response.first_name);
                $('#studentEmail').text(response.personal_email);
                $('#studentMobile').text(response.phone_number);
                // $('#studentDetails').show();
            },
            error: function(error) {
                console.error('Error fetching student details:', error);
            }
        });
    });

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
        const cleanedJson = daysJson.replace(/^"|"$/g, '').replace(/\\"/g, '"');
        const daysArray = JSON.parse(cleanedJson);

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

    function initializeSectionSelect2(selector) {
        $(selector).select2({
            width: 'resolve',
            placeholder: "Select Section",
            allowClear: true,
            minimumInputLength: 0,
            ajax: {
                url: searchSecSub, 
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    let subject_id = $(selector).closest('tr').attr('data-subject-id');
                    return {
                        search: params.term,
                        acad_year: $('#acad_year').val(),
                        term: $('#term').val(),
                        year_level: $('#year_level').val(),
                        subject_id: subject_id
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.map(function(item) {
                            return {
                                id: item.section_id,
                                text: item.section_name + ' - ' + item.section_type,
                                customData: item
                            };
                        })
                    };
                },
                cache: true
            }
        }).on("select2:select",function(e){
            let selectedData = e.params.data.customData;
            let subjectId = $(this).closest('tr').attr('data-subject-id');
            let secSubId = selectedData.sec_sub_id;
            // selections[subjectId] = secSubId;
            let existingSelectionIndex = selections.findIndex(selection => selection.subject_id === subjectId);
            if (existingSelectionIndex !== -1) {
                // Update existing selection with new sec_sub_id
                selections[existingSelectionIndex].sec_sub_id = secSubId;
            } else {
                // Push new selection
                selections.push({ subject_id: subjectId, sec_sub_id: secSubId });
            }
            console.log("Updated selections:", selections);
            console.log("Selected sec_sub_id:", secSubId);
            console.log("SecSubIDS:", selectedSecSubIds);
            console.log("SecSubIDSasdasd");
            let f2fTimeSchedule = formatTime(selectedData.start_time_f2f, selectedData.end_time_f2f);
            let onlineTimeSchedule = formatTime(selectedData.start_time_online, selectedData.end_time_online);
            let formattedClassDaysF2F = formatDays(selectedData.class_days_f2f || '[]');
            let formattedClassDaysOnline = formatDays(selectedData.class_days_online || '[]');    
            let $row = $(this).closest('tr');
            let hiddenInputForSecSubId = $("<input>").attr({
                type: "hidden",
                name: "sec_sub_ids[]", 
                value: selectedData.sec_sub_id
            });
            $row.find('.f2f-time-cell').text(f2fTimeSchedule);
            $row.find('.online-time-cell').text(onlineTimeSchedule);
            $row.find('.f2f-days-cell').text(formattedClassDaysF2F);
            $row.find('.online-days-cell').text(formattedClassDaysOnline);
            $row.find('.section-name').text(selectedData.section_name + ' - ' + selectedData.section_type);
            $row.append(hiddenInputForSecSubId);
        });
    }

    $('.test-enroll').on('click', function() {
        console.log("Final selections for submission:", JSON.stringify(selections));
    
        $.ajax({
            url: "/admin/enrollments/validate",
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify({
                selections: selections
            }),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                alert('Test enrolled');
                console.log('Validation Success:', response);
            },
            error: function(xhr, status, error) {
                console.error('Error validation:', error);
                alert('Error during validation');
            }
        });
    });
    

    updateTotalUnits();
});
