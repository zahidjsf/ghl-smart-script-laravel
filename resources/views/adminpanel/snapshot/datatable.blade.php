<script>
$(function() {
    $('#Yajra-dataTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("admin.get-snapshots") }}',
        columns: [
            { data: 'image', name: 'image', orderable: false, searchable: false},
            { data: 'project_name', name: 'project_name' },
            { data: 'name', name: 'name' },
            { data: 'url', name: 'url' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        dom: 'Bfrtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
        responsive: true
    });
});
</script>
