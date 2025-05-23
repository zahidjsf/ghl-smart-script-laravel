@extends('frontpanel.layout.app')

@section('content')
@section('smart_apps', 'active')

<div class="container-fluid" style="padding:0 1.5rem">
    <!-- start page title -->

   <div class="d-flex justify-content-between align-items-center">
        <h2 class="mb-0">Custom Value Updater</h2>
        <a href="#" class="btn btn-primary mb-2">Training</a>
    </div>
    <hr>


    <div class="d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Custom Value Collections</h5>
        <a href="{{ route('frontend.smart_reward.addcollection') }}" class="btn btn-success mb-2">+ Create New Collection</a>
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
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Action</th>
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
                    // Inject the modal content
                    $('#duplicateLocation-modal').html(response.view);

                    // Show the modal
                    $('#duplicateLocationModal').modal('show');
                },
                error: function(xhr) {
                    alert('Error loading modal content');
                    console.error(xhr.responseText);
                }
            });
        });

                $(document).on('click', '.remove-collection', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
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

                            }else{
                                var msg = document.getElementById("error-msg");
                                msg.removeAttribute('hidden');
                                msg.innerText = response.message;
                            }

                        },
                        error: function(xhr) {
                            alert('Error loading modal content');
                            console.error(xhr.responseText);
                        }
                    });




                }
            });




        });








</script>
@endsection
