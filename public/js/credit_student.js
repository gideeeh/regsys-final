$(document).ready(function() {
    $('#student').select2({
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

    
    $('#subject').select2({
        width: 'resolve',
        placeholder: "Subject Code or Name",
        allowClear: true,
        minimumInputLength: 1,
        ajax: {
            url: getSubjectsUrl ,
            dataType: 'json',
            delay: 20,
            processResults: function (data) {
                return {
                    results: data.map(function (item) {
                        return {
                            id: item.subject_id,
                            text: item.subject_code + ' - ' + item.subject_name
                        };
                    })
                };
            },
            cache: true
        }
    });
    
    // .on("select2:select", function(e) {
    //     var studentId = e.params.data.id;
    //     $.ajax({
    //         url: `/admin/students/get-students/${studentId}`,
    //         type: "GET",
    //         success: function(response) {
    //             $('#student_id').val(response.student_id);
    //             $('#first_name').val(response.first_name);
    //             $('#middle_name').val(response.middle_name);
    //             $('#last_name').val(response.last_name);
    //             $('#suffix').val(response.suffix);
    //             $('#student_number').val(response.student_number);
    //             $('#studentName').text(response.first_name);
    //             $('#studentEmail').text(response.personal_email);
    //             $('#studentMobile').text(response.phone_number);
    //             // $('#studentDetails').show();
    //         },
    //         error: function(error) {
    //             console.error('Error fetching student details:', error);
    //         }
    //     });
    // });
});