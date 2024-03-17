$(document).ready(function() {
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

    $('#subject_to_add').select2({
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
                            // subject_code: item.subject_code,
                            // subject_name: item.subject_name,
                            // units_lec: item.units_lec,
                            // units_lab: item.units_lab,
                            // total_units: item.units_lec + item.units_lab
                        };
                    })
                };
            },
            cache: true
        }
    });
});