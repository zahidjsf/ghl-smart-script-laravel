@section('js-script-add')

<script>
    $(function () {
        $('#locations-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('frontend.smart_reward.getlocations') }}",
            columns: [
                { data: 'name', name: 'name' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    });
    </script>

@endsection
