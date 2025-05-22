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
</script>
@endsection
