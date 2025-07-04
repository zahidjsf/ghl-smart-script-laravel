<script>
      $(function() {
        $('#Yajra-dataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("admin.get-projects") }}',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'description', name: 'description' },
                { data: 'url', name: 'url' },
                { data: 'inMembership', name: 'inMembership' },
                { data: 'license_url', name: 'license_url' },
                { data: 'cv_collections', name: 'cv_collections' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            responsive: true
        });
    });
</script>
