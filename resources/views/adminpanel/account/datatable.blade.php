<script>
    $(function() {
        $('#Yajra-dataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("admin.get-accounts") }}',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'username', name: 'username' },
                { data: 'name', name: 'name', orderable: false },
                { data: 'license_short', name: 'licensekey', className: 'responsive-hidden' },
                { data: 'role', name: 'role', className: 'responsive-hidden' },
                { data: 'suspend', name: 'suspend' },
                { data: 'parent_id', name: 'detail.parent_id', className: 'responsive-hidden' },
                {
                    data: 'actions',
                    name: 'actions',
                    orderable: false,
                    searchable: false,
                    className: 'text-center'
                }

            ],
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            responsive: true
        });
    });
</script>
