$(document).ready(function() {
    $('.allowedFileExtension').select2({
        width: 'resolve',
        allowClear: true,
        minimumInputLength: 0,
    });
    $('.js-example-basic-multiple').select2();

    $('#requireUpload').change(function() {
        if ($(this).is(':checked')) {
            $('#allowedFileExtension').prop('disabled', false).prop('required', true);
            $("#max-size").prop('readonly', false).prop('required', true); 
        } else {
            $('#allowedFileExtension').prop('disabled', true).prop('required', false);
            $("#max-size").prop('readonly', true).prop('required', false); 
        }
    });

    $('#updateRequireUpload').change(function() {
        if ($(this).is(':checked')) {
            $('#update_allowedFileExtension').prop('disabled', false).prop('required', true);
            $("#update_max-size").prop('readonly', false).prop('required', true); 
        } else {
            $('#update_allowedFileExtension').prop('disabled', true).prop('required', false);
            $("#update_max-size").prop('readonly', true).prop('required', false); 
        }
    });
});
