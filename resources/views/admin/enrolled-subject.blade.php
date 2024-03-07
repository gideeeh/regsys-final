<form id="autoSubmitForm" method="POST" action="{{ url('admin/enrollments/enroll/enroll_subjects/' . session('enrollment_id')) }}">
    @csrf
</form>
<script>
    // Automatically submit the form when the document is ready
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('autoSubmitForm').submit();
    });
</script>