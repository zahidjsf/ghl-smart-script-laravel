@extends('adminpanel.layout.app')

@section('content')
@section('select_project', 'active')
<div class="row">
    <div class="col-md-12">
        <div class="card card-round">
            <div class="card-header">
                <div class="card-head-row card-tools-still-right">
                    <h4 class="card-title">Projects</h4>
                    <div class="card-tools">
                        <a href="{{ route('admin.projectcreate') }}" class="btn btn-primary">Add New Project</a>
                    </div>
                </div>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">

                        <table id="Yajra-dataTable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Project</th>
                                    <th>Description</th>
                                    <th>Parent</th>
                                    <th>In Membershipt ?</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be populated by DataTables -->
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js-script')
@include('adminpanel.project.datatable')
@endsection
