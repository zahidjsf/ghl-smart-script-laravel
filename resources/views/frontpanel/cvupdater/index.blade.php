@extends('frontpanel.layout.app')

@section('content')
@section('smart_apps', 'active')

<div class="container-fluid" style="padding:0 1.5rem">
    <!-- start page title -->
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="mb-0">{{ __('messages.cv_updater') }}</h2>
        <a href="#" class="btn btn-primary mb-2">{{ __('messages.training') }}</a>
    </div>
    <hr>
    <div class="d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{{ __('messages.cv_collection') }}</h5>
        <a href="{{ route('frontend.smart_reward.addcollection') }}" class="btn btn-success mb-2">{{ __('messages.create_collection') }}</a>
    </div>
    <div class="alert alert-success" id="success-msg" hidden></div>
    <div class="alert alert-danger" id="error-msg" hidden></div>

    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="content-block">
                <table class="table table-bordered" id="collections-table">
                    <thead>
                        <tr>
                            <th>{{ __('messages.id') }}</th>
                            <th>{{ __('messages.name') }}</th>
                            <th>{{ __('messages.description') }}</th>
                            <th>{{ __('messages.action') }}</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div id="duplicateLocation-modal"></div>
</div>
@endsection

@section('js-script-add')
<script>
    $(function() {
        $('#collections-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('frontend.smart_reward.getcollections') }}",
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'description',
                    name: 'description'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ]
        });
    });

    $(document).on('click', '.duplicate-collection', function(e) {
        e.preventDefault();
        let url = $(this).data('url');

        $.ajax({
            url: url,
            method: 'GET',
            success: function(response) {
                $('#duplicateLocation-modal').html(response.view);
                $('#duplicateLocationModal').modal('show');
            },
            error: function(xhr) {
                alert('{{ __("messages.loading_error") }}');
                console.error(xhr.responseText);
            }
        });
    });
    $(document).on('click', '.remove-collection', function(e) {
        e.preventDefault();
        Swal.fire({
            title: '{{ __("messages.are_you_sure") }}',
            text: "{{ __("messages.delete_confirm_text") }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '{{ __("messages.delete_confirm_button") }}',
            cancelButtonText: '{{ __("messages.delete_cancel_button") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                let url = $(this).data('url');
                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function(response) {
                        if (response.status == 'success') {
                            var table = $('#collections-table').DataTable();
                            table.draw();
                            var msg = document.getElementById("success-msg");
                            msg.removeAttribute('hidden');
                            msg.innerText = response.message;
                        } else {
                            var msg = document.getElementById("error-msg");
                            msg.removeAttribute('hidden');
                            msg.innerText = response.message;
                        }
                    },
                    error: function(xhr) {
                        alert('{{ __("messages.loading_error") }}');
                        console.error(xhr.responseText);
                    }
                });
            }
        });
    });
</script>
@endsection
