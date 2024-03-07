$(document).ready(function() {
    $('#stu-appointment').select2({
        width: 'resolve',
        placeholder: "Appointment ID or Student Name",
        allowClear: true,
        minimumInputLength: 1,
        ajax: {
            url: getStudentsUrl,
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
    });

    $('#request-service').select2({
        width: 'resolve',
        placeholder: 'Select Service',
        allowClear: true,
        minimumInputLength: 0,
        ajax: {
            url: servicesUrl,
            dataType: 'json',
            delay: 20,
            processResults: function (data) {
                return {
                    results: data.map(function(item) {
                        return {
                            id: item.id,
                            text: item.service_name
                        };
                    })
                };
            },
            cache: true
        }
    }).on("select2:select", function(e) {
        let serviceData = e.params.data.id;
        $("#request-service").val(serviceData);
        console.log(`${serviceData}`);
    });

    $('#hello').on('click', function() {
        alert("Hello");
    });

    $('#btnPending').on('click', function() {
        $(this).removeClass('bg-gray-500 hover:bg-gray-600').addClass('bg-red-500 hover:bg-red-600');
        $('#btnCompleted').removeClass('bg-red-500 hover:bg-red-600').addClass('bg-gray-500 hover:bg-gray-600');
        $.ajax({
            url: appointmentsPending,
            method: 'GET',
            success: function(response) {
                updateTableRows(response);
            },
            error: function(xhr, status, error) {
                console.error("Error fetching pending appointments:", error);
            }
        });
    });

    $('#btnCompleted').on('click', function() {
        $(this).removeClass('bg-gray-500 hover:bg-gray-600').addClass('bg-red-500 hover:bg-red-600');
        $('#btnPending').removeClass('bg-red-500 hover:bg-red-600').addClass('bg-gray-500 hover:bg-gray-600');
        $.ajax({
            url: appointmentsComplete,
            method: 'GET',
            success: function(response) {
                updateTableRows(response);
            },
            error: function(xhr, status, error) {
                console.error("Error fetching completed appointments:", error);
            }
        });
    });

    function updateTableRows(appointments) {
        var tbody = $('table tbody');
        tbody.empty();

        if(appointments.length === 0) {
            tbody.append('<tr><td colspan="3" class="text-center">No data available</td></tr>');
            return;
        }

        appointments.forEach(function(appointment) {
            tbody.append(`<tr class="border-b hover:bg-gray-100 cursor-pointer">
                            <td class="border-dashed border-t border-gray-300 p-2">${appointment.service_name}</td>
                            <td class="border-dashed border-t border-gray-300 p-2">${appointment.appointment_datetime}</td>
                            <td class="border-dashed border-t border-gray-300 p-2">${appointment.status}</td>
                          </tr>`);
        });
    };

    
});