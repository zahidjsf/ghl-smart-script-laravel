<script>
      $(function() {
        $('#api-history-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("frontend.get-apihistory") }}',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'loc_id', name: 'loc_id' },
                { data: 'status', name: 'status' },
                { data: 'date', name: 'date' },
                { data: 'type', name: 'type' },
                { data: 'notes', name: 'notes' },
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
